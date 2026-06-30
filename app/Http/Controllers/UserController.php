<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly UserService $service) {}

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        return view('users.index', ['users' => $this->service->all()]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create');
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->service->create($request->validated());

        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ], 201);
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $updated = $this->service->update($user, $request->validated(), $request->user());

        return response()->json([
            'id'   => $updated->id,
            'name' => $updated->name,
            'role' => $updated->role,
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        $this->service->delete($user);

        return response()->json(null, 204);
    }
}
