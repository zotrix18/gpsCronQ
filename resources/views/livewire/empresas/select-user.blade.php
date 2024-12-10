<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $title }}</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('empresas.index') }}">Empresas</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    Listado de usuarios
                </li>
            </ol>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-header border-bottom d-flex">
                    <div class="main-header-center d-none d-xl-block">
                        <!-- Campo de búsqueda enlazado a la propiedad Livewire -->
                        <form class="form-horizontal">
                            <input class="form-control" wire:model.live="querySearch" placeholder="Buscar" />
                        </form>
                    </div>
                    <div class="d-flex order-lg-2 ms-auto header-right-icons">
                        <button class="btn btn-primary btn-icon active text-end" data-bs-toggle="modal"
                            data-bs-target="#selectUser">
                            <i class="mx-2 fa fa-plus"></i>
                            Asignar usuario
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border text-nowrap text-md-nowrap table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Telefono</th>
                                    <th class="text-end">Opciones</th>
                                </tr>
                            </thead>
                            <tbody wire:loading.class="opacity-50">
                                @forelse ($empresa->users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->telefono ? $user->telefono : '-' }}</td>
                                        <td class="text-end">
                                            <div aria-label="Basic example" class="my-1 btn-group btn-group-sm"
                                                role="group">

                                                <!-- Botón para cambiar el estado -->
                                                <button class="btn btn-danger btn-icon active" data-bs-placement="top"
                                                    data-bs-toggle="tooltip" data-bs-original-title="Quitar usuario"
                                                    wire:click="showConfirm({{ $user->id }})">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Sin registros para mostrar...</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal de selección de usuarios -->
    <div class="modal fade" id="selectUser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Selección de usuarios:</h6>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <!-- Campo de búsqueda enlazado a la propiedad Livewire -->
                    <form class="form-horizontal">
                        <div class="form-group">
                            <div class="input-group">
                                <input class="form-control" id="basic-addon6" placeholder="Buscar por nombre o correo"
                                    type="text" wire:model="querySearch">
                                <button class="btn btn-primary text-white" type="button"><i
                                        class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>

                    <p style="margin-top: 30px">Para seleccionar un usuario haga click en algún registro.</p>
                    <table class="table border text-nowrap text-md-nowrap table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nombre</th>
                                <th>Correo</th>
                            </tr>
                        </thead>
                        <tbody wire:loading.class="opacity-50">
                            @forelse ($users as $user)
                                <tr style="cursor:pointer;" onclick="toggleCheckbox({{ $user->id }})">
                                    <td>
                                        <label class="ckbox">
                                            <input type="checkbox" class="user-checkbox"
                                                id="checkbox-{{ $user->id }}" value="{{ $user->id }}"
                                                style="cursor:pointer !important;"><span />
                                        </label>
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay usuarios disponibles para asignar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <!-- Botón que enviará los datos seleccionados a Livewire -->
                    <button class="btn btn-primary" id="btnSelectU" disabled onclick="submitSelectedUsers()">
                        Seleccionar
                    </button>
                </div>
            </div>
        </div>
    </div>


</div>

@section('scripts')
    <!-- INTERNAL Notifications js -->
    <script src="{{ asset('assets/plugins/notify/js/rainbow.js') }}"></script>
    <script src="{{ asset('assets/plugins/notify/js/sample.js') }}"></script>
    <script src="{{ asset('assets/plugins/notify/js/jquery.growl.js') }}"></script>
    <script src="{{ asset('assets/plugins/notify/js/notifIt.js') }}"></script>
@endsection

{{-- para solucinar el error de cargando infinito --}}
@script
    <script>
        const loader = document.getElementById('global-loader')
        const modal = new bootstrap.Modal(document.getElementById('selectUser'))
        let selectedUsers = []

        // modal.show()
        // Seleccionar todos los checkboxes
        const checkboxes = document.querySelectorAll('.user-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                handleCheckboxChange(this)
            });
        });

        function handleCheckboxChange(checkbox) {
            const row = checkbox.closest('tr'); // Seleccionar la fila donde está el checkbox
            if (checkbox.checked) {
                // Si el checkbox está seleccionado, agregar el ID al array
                selectedUsers.push(checkbox.value);
                // Añadir clase a la fila para cambiar el fondo
                row.style = "cursor: pointer; background: var(--primary01) !important"
            } else {
                // Si el checkbox se desmarca, eliminar el ID del array
                selectedUsers = selectedUsers.filter(id => id !== checkbox.value);
                // Quitar clase de la fila
                row.style = "cursor: pointer"
            }

            // Activar o desactivar el botón "Seleccionar" basado en la selección
            document.getElementById('btnSelectU').disabled = selectedUsers.length === 0;
        }

        // Función para alternar el estado del checkbox al hacer clic en la fila
        function toggleCheckbox(userId) {
            const checkbox = document.getElementById(`checkbox-${userId}`);
            checkbox.checked = !checkbox.checked; // Cambiar el estado del checkbox

            // Llamar a la función para manejar el cambio de estado
            handleCheckboxChange(checkbox);
        }

        function submitSelectedUsers() {
            // Llamar al método de Livewire para asignar usuarios
            Livewire.dispatch('assignUsers', {
                selectedUsers: selectedUsers
            });

            // Limpiar la selección después de enviar
            selectedUsers = [];
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
                checkbox.closest('tr').style = "cursor: pointer";
            });
            document.getElementById('btnSelectU').disabled = true;
        }


        // Hacer la función toggleCheckbox disponible globalmente
        window.toggleCheckbox = toggleCheckbox;
        window.submitSelectedUsers = submitSelectedUsers;


        // Aplica la transición de opacidad
        setTimeout(() => {
            loader.classList.add('fade-out');

            setTimeout(() => loader.classList.add('d-none'), 500)
        }, 250);


        $wire.on('successAlert', (event) => {
            window.$.growl.notice({
                title: 'Operación exitosa!',
                message: event?.message || ""
            })

            modal.hide();
        })

        $wire.on('showConfirm', (event) => {
            swal({
                    title: "¿Estás seguro?",
                    text: "Vas a quitar el usuario.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Si continuar",
                    cancelButtonText: "Cancelar",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm) {
                    if (isConfirm) {
                        $wire.dispatch('removeUser', {
                            userId: event?.id
                        });
                    }
                });

        });
    </script>
@endscript
