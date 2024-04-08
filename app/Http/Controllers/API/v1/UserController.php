<?php

namespace App\Http\Controllers\API\v1;

use App\Actions\User\CreateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Actions\User\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request, CreateUserAction $createUserAction)
    {
        try {
            Gate::authorize('create', User::class);
    
            $data = $request->validated();
            $user = $createUserAction->execute($data);
            $userResource = new UserResource($user);
    
            return HttpResponse::success(201, 'User created successfully', $userResource);
        } catch (AuthorizationException $e) {
            return HttpResponse::error(403, 'You are unauthorized to create user.');
        } catch (\Exception $e) {
            return HttpResponse::error(500, 'An error occurred while creating user.');
        }
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        try {
            Gate::authorize('view', $user);
    
            $userResource = new UserResource($user);
    
            return HttpResponse::success(200, 'User retrieved successfully', $userResource);
        } catch (AuthorizationException $e) {
            return HttpResponse::error(403, 'You are unauthorized to view user.');
        } catch (\Exception $e) {
            return HttpResponse::error(500, 'An error occurred while retrieving user.');
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $updateUserAction)
    {
        try {
            Gate::authorize('update', $user);
    
            $data = $request->validated();
            $user = $updateUserAction->execute($user, $data);
            $userResource = new UserResource($user);
    
            return HttpResponse::success(200, 'User updated successfully', $userResource);
        } catch (AuthorizationException $e) {
            return HttpResponse::error(403, 'You are unauthorized to update user.');
        } catch (\Exception $e) {
            return HttpResponse::error(500, 'An error occurred while updating user.');
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, DeleteUserAction $deleteUserAction)
    {
        try {
            Gate::authorize('delete', $user);
    
            $deleteUserAction->execute($user);
    
            return HttpResponse::success(200, 'User deleted successfully');
        } catch (AuthorizationException $e) {
            return HttpResponse::error(403, 'You are unauthorized to delete user.');
        } catch (\Exception $e) {
            return HttpResponse::error(500, 'An error occurred while deleting user.');
        }
    }
}
