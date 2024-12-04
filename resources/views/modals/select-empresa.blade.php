<div>
    <div class="modal fade" id="seleccionarEmpresa" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Seleccione una Empresa:</h6>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ul class="nav1 nav-column flex-column br-7" style="border: none">
                        @if (!Auth::check() || Auth::user()->empresas->count() == 0)
                            <p>No tienes empresas asociadas.</p>
                        @else
                            @foreach (Auth::user()->empresas as $empresa)
                                <li class="nav-item1">
                                    <a class="nav-link thumb" href="#"
                                        onclick="selectCompany(this, '{{ $empresa->id }}')">{{ $empresa->empresa }}</a>
                                </li>
                            @endforeach
                        @endif

                    </ul>

                    <form id="empresaForm" action="{{ route('store.empresa') }}" method="POST">
                        @csrf
                        <input type="hidden" name="empresa_id" id="empresa_id" value="">
                    </form>
                </div>
                <div class="modal-footer">
                    {{-- <button class="btn btn-light" data-bs-dismiss="modal">Cerrar</button> --}}

                    <button class="btn btn-primary" data-bs-target="#modalToggle2" data-bs-toggle="modal" id="btnSelect"
                        disabled data-bs-dismiss="modal" onclick="submitForm()">
                        Seleccionar
                    </button>
                </div>
            </div>
        </div>
    </div>



    <script>
        // Función para manejar la selección de una empresa
        function selectCompany(element, empresaId) {
            // Resaltar la empresa seleccionada (opcional)
            const links = document.querySelectorAll('.nav-link');
            links.forEach(link => link.classList.remove('active')); // Limpiar clase activa
            element.classList.add('active'); // Agregar clase activa al enlace seleccionado

            // Habilitar el botón "Seleccionar"
            const btnSelect = document.getElementById('btnSelect');
            btnSelect.disabled = false;

            // Actualizar el ID de la empresa seleccionada
            document.getElementById('empresa_id').value = empresaId;
        }


        // Función para enviar el formulario
        function submitForm() {
            document.getElementById('empresaForm').submit();
        }
    </script>
</div>
