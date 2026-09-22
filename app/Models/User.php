<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements CanResetPasswordContract
{
    /** @use HasFactory<UserFactory> */
    use CanResetPasswordTrait, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_type',
        'status',
        'preferred_locale',
        'last_login_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->using(RoleUser::class)
            ->withPivot('assigned_by')
            ->withTimestamps();
    }

    public function authoredNews(): HasMany
    {
        return $this->hasMany(News::class, 'author_id');
    }

    public function editedNews(): HasMany
    {
        return $this->hasMany(News::class, 'editor_id');
    }

    public function submittedEditorialWorkflows(): HasMany
    {
        return $this->hasMany(EditorialWorkflow::class, 'submitted_by');
    }

    public function reviewedEditorialWorkflows(): HasMany
    {
        return $this->hasMany(EditorialWorkflow::class, 'reviewed_by');
    }

    public function uploadedMediaFiles(): HasMany
    {
        return $this->hasMany(MediaFile::class, 'uploaded_by');
    }

    public function createdPageBlocks(): HasMany
    {
        return $this->hasMany(PageBlock::class, 'created_by');
    }

    public function updatedPageBlocks(): HasMany
    {
        return $this->hasMany(PageBlock::class, 'updated_by');
    }

    public function createdMenus(): HasMany
    {
        return $this->hasMany(Menu::class, 'created_by');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    public function assignedContactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class, 'assigned_to');
    }

    public function visitorAnalytics(): HasMany
    {
        return $this->hasMany(VisitorAnalytic::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function scopeStaff(Builder $query): Builder
    {
        return $query->where('user_type', 'staff');
    }

    public function isStaff(): bool
    {
        return $this->user_type === 'staff';
    }

    public function isPublic(): bool
    {
        return $this->user_type === 'public';
    }

    public function canAccessAdmin(): bool
    {
        return $this->isStaff() && $this->roles()->exists();
    }

    public function canAccessPublicAccount(): bool
    {
        return $this->isPublic() && $this->status === 'active';
    }

    public function hasRole(string $roleSlug): bool
    {
        $this->loadMissing('roles');

        return $this->roles->contains(fn (Role $role) => $role->slug === $roleSlug);
    }

    public function hasAnyRole(array $roleSlugs): bool
    {
        $this->loadMissing('roles');

        return $this->roles->contains(fn (Role $role) => in_array($role->slug, $roleSlugs, true));
    }

    public function permissions(): Collection
    {
        $this->loadMissing('roles.permissions');

        return $this->roles
            ->flatMap(fn (Role $role) => $role->permissions)
            ->unique('id')
            ->values();
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        return $this->permissions()->contains(
            fn (Permission $permission) => $permission->slug === $permissionSlug
        );
    }

    public function hasAnyPermission(array $permissionSlugs): bool
    {
        foreach ($permissionSlugs as $permissionSlug) {
            if ($this->hasPermission($permissionSlug)) {
                return true;
            }
        }

        return false;
    }
}
