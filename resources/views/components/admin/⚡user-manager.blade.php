<?php

use App\Enums\UndpUserRole;
use App\Models\UndpUser;
use Livewire\Component;

new class extends Component
{
    /** @var \Illuminate\Database\Eloquent\Collection<int, UndpUser> */
    public $users;

    public string $name = '';

    public string $email = '';

    public string $role = 'analyst';

    public string $password = '';

    public function mount(): void
    {
        $this->loadUsers();
    }

    public function loadUsers(): void
    {
        $this->users = UndpUser::latest()->get();
    }

    public function createUser(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:undp_users,email',
            'role' => 'required|in:field_coordinator,analyst,operator,superadmin',
            'password' => 'required|string|min:8',
        ]);

        UndpUser::create([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'password' => $this->password,
            'is_active' => true,
        ]);

        $this->reset(['name', 'email', 'role', 'password']);
        $this->loadUsers();
    }

    public function toggleActive(string $id): void
    {
        $user = UndpUser::findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);
        $this->loadUsers();
    }

    public function deleteUser(string $id): void
    {
        UndpUser::findOrFail($id)->delete();
        $this->loadUsers();
    }
};
?>

<div class="space-y-6">
    {{-- Create form --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-h3 font-heading font-semibold text-slate-900 mb-4">Add UNDP User</h2>
        <form wire:submit="createUser" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-atoms.text-input
                    name="name"
                    label="Full name"
                    placeholder="e.g. Emmanuel Adjei"
                    :required="true"
                    :error="$errors->first('name')"
                    wire:model="name"
                />
                <x-atoms.text-input
                    name="email"
                    label="Email"
                    type="email"
                    placeholder="user@undp.org"
                    :required="true"
                    :error="$errors->first('email')"
                    wire:model="email"
                />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-atoms.select
                    name="role"
                    label="Role"
                    :required="true"
                    :options="[
                        'field_coordinator' => 'Field Coordinator',
                        'analyst' => 'Analyst',
                        'operator' => 'Operator',
                        'superadmin' => 'Superadmin',
                    ]"
                    :error="$errors->first('role')"
                    wire:model="role"
                />
                <x-atoms.text-input
                    name="password"
                    label="Password"
                    type="password"
                    placeholder="Min 8 characters"
                    :required="true"
                    :error="$errors->first('password')"
                    wire:model="password"
                />
            </div>
            <x-atoms.button type="submit" variant="primary">
                Create User
            </x-atoms.button>
        </form>
    </div>

    {{-- Users table --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-h4 font-heading font-semibold text-slate-900">All Users</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-body-sm text-left">
                <thead class="bg-surface-page">
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 font-medium text-slate-600">Name</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Email</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Role</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Status</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <x-atoms.badge variant="{{ match($user->role->value) {
                                    'superadmin', 'operator' => 'verified',
                                    'analyst' => 'synced',
                                    'field_coordinator' => 'pending',
                                    default => 'default',
                                } }}">
                                    {{ ucwords(str_replace('_', ' ', $user->role->value)) }}
                                </x-atoms.badge>
                            </td>
                            <td class="px-4 py-3">
                                @if($user->is_active)
                                    <x-atoms.badge variant="synced">Active</x-atoms.badge>
                                @else
                                    <x-atoms.badge variant="draft">Inactive</x-atoms.badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <x-atoms.button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="toggleActive('{{ $user->id }}')"
                                    >
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </x-atoms.button>
                                    <x-atoms.button
                                        variant="danger"
                                        size="sm"
                                        wire:click="deleteUser('{{ $user->id }}')"
                                        wire:confirm="Are you sure you want to delete this user?"
                                    >
                                        Delete
                                    </x-atoms.button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">No users configured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
