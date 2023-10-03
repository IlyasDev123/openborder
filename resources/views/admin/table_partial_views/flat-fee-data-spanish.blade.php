 <table class="table table-striped">
     <thead>
         <tr>
             <th>
                 Categoría
             </th>
             <th>
                 Nombre de tarifa plana
             </th>
             <th>
                 Tipo de plan de paquete
             </th>
             <th>
                 Duración
             </th>
             <th>
                 Períodos recurrentes
             </th>

             <th>
                 Monto
             </th>
             <th>
                 Descripción
             </th>
             <th>
                 estado
             </th>
             <th>
                 Imagen
             </th>
             <th>
                 Acción
             </th>
         </tr>
     </thead>
     <tbody id="tablecontents" >
         <tr class="row1" data-id="{{ $package->id }}">
             <td>
                 {{ $package->package->package_name_es ?? '' }}
             </td>
             <td>
                 {{ $package->plan_name_es ?? '' }}
             </td>
             <td>
                 {{ $package->plan_type_es ?? '' }}
             </td>
             <td>
                 {{ $package->duration ?? '' }}
             </td>
             <td>
                 {{ $package->recurring_period_es ?? '' }}
             </td>
             <td>{{ $package->amount ?? '' }}</td>

             <td>
                 @php
                     $description_es = strip_tags($package->description_es);
                 @endphp
                 {{ \Illuminate\Support\Str::limit(html_entity_decode($description_es), 100, '...') }}
             </td>

             <td>
                 @if ($package->status != 1)
                     <form action="{{ route('package.plan.active') }}" id="" method="post">
                         @csrf
                         <input type="hidden" name="id" value="{{ $package->id }}" id="id">
                         <input type="hidden" name="status" value="1">
                         <button type="submit" class="btn btn-primary btn-sm">Activar</button>
                     </form>
                 @else
                     <form action="{{ route('package.plan.deactivate') }}" id="" method="post">
                         @csrf
                         <input type="hidden" name="id" value="{{ $package->id }}" id="id">
                         <input type="hidden" name="status" value="0">
                         <button type="submit" class="btn btn-danger btn-sm">
                             Desactivar</button>
                     </form>
                 @endif

             </td>
             <td>
                 <img class="m-2" src="{{ asset($package->image) }}" alt="" width="100" height="100">
             </td>
             <td>
                 {{-- <a href="{{ route('admin.manage.show.package', [$user->id]) }}" title="View User">
                                    <i class="fas fa-eye"></i>
                                </a> --}}
                 <a href="{{ route('package.plan.edit', $package->id) }}" title="update pricing plan">
                     <i class="fas fa-edit m-3"></i>
                 </a>
                 {{-- <a href="{{ route('package.plan.delete') }}" data-id="{{ $package->id }}"
                                class="delete_user" title="Delete User" data-toggle="modal"
                                data-target="#delete_user_modal">
                                <i class="fas fa-trash m-3"></i> --}}
                 {{-- </a> --}}
             </td>
         </tr>
     </tbody>
 </table>
