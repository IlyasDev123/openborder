
<table id="users_list" class="table table-bordered table-hover">
	<thead>
		<tr>
			<th>
				Name
			</th>
			<th>
				Email
			</th>
			<th>
				Type
			</th>
			<th>
				Created At
			</th>
			<th>
				Actions
			</th>
		</tr>
	</thead>
	<tbody>
		@foreach($users as $user)
			<tr>
				<td>
					<img src="{{ user_img($user->image) }}" class="direct-chat-img" alt="User Image">
				<span class="m-4">	{{ $user->first_name.' '.$user->last_name }} </span>
				</td>
				<td>
					{{ $user->email }}
				</td>
				<td>
					@foreach($user->roles()->pluck('name') as $role)
						<span class="badge badge-info">{{ $role }}</span>
					@endforeach
				</td>
				<td>
					{{ $user->created_at }}
				</td>
				{{-- <td>
					{!! admin_user_type_badge($user->type) !!}
				</td> --}}  
				<td>
					@if(is_allowed('active_user'))
					@if($user->is_blocked != App\Helpers\Constants::STATUS_ACTIVE || $user->is_blocked == App\Helpers\Constants::STATUS_INACTIVE || $user->is_blocked == App\Helpers\Constants::STATUS_BLOCKED || $user->is_blocked == App\Helpers\Constants::STATUS_SUSPENDED)
						<a href="{{ route('admin.active.user', [$user->id]) }}" class="active_user" title="Active User" data-toggle="modal" data-target="#active_user_modal">
							<i class="fas fa-user-plus"></i>
						</a>
					@endif
						@endif
					{{-- @if($user->is_blocked == App\Helpers\Constants::STATUS_ACTIVE || $user->is_blocked != App\Helpers\Constants::STATUS_INACTIVE || $user->is_blocked == App\Helpers\Constants::STATUS_BLOCKED || $user->is_blocked == App\Helpers\Constants::STATUS_SUSPENDED)
						<a href="{{ route('admin.inactive.user', [$user->id]) }}" class="inactive_user" title="In-Active User" data-toggle="modal" data-target="#inactive_user_modal">
							<i class="fas fa-user-slash"></i>
						</a>
					@endif --}}
					@if(is_allowed('user_can_block'))
					@if($user->is_blocked == App\Helpers\Constants::STATUS_ACTIVE || $user->is_blocked == App\Helpers\Constants::STATUS_INACTIVE || $user->is_blocked != App\Helpers\Constants::STATUS_BLOCKED || $user->is_blocked == App\Helpers\Constants::STATUS_SUSPENDED)
						<a  href="{{ route('admin.blocked.user', [$user->id]) }}" class="ban_user" title="Blocked User" data-toggle="modal" data-target="#ban_user_modal">
							<i class="fas fa-ban"></i>
						</a>
					@endif
					@endif
{{-- 	@
					@if($user->is_blocked == App\Helpers\Constants::STATUS_ACTIVE || $user->is_blocked == App\Helpers\Constants::STATUS_INACTIVE || $user->is_blocked != App\Helpers\Constants::STATUS_BLOCKED || $user->is_blocked == App\Helpers\Constants::STATUS_SUSPENDED)
						<a  href="{{ route('admin.delete.user', [$user->id]) }}" class="delete_user" title="Delete User" data-toggle="modal" data-target="#suspend_user_modal">
							<i class="fas fa-user-lock"></i>
						</a>
					@endif --}}
					<a href="{{ route('admin.show.user', [$user->id]) }}" title="View User">
						<i class="fas fa-eye"></i>
					</a>
					<a href="{{ route('admin.edit.user', [$user->id]) }}" title="Edit User">
						<i class="fas fa-edit"></i>
					</a>

				
					{{-- <a href="{{ route('admin.delete.user') }}" data-id="{{ $user->id }}" class="delete_user" title="Delete User" data-toggle="modal" data-target="#delete_user_modal">
						<i class="fas fa-trash"></i>
					</a> --}}
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
