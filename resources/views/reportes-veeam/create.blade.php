<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $reporte ? 'Editar' : 'Ingresar' }} Reporte Veeam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <style>
        .ts-control {
            border-radius: 0.375rem !important;
            border-color: rgb(209 213 219) !important;
            padding: 0.5rem !important;
            background-color: white !important;
        }
        .ts-wrapper {
            border-radius: 0.375rem;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center py-12">

<div class="flex w-full max-w-6xl bg-white p-8 rounded-lg shadow-lg border border-red-200">
    {{-- Menú lateral --}}
    <aside class="w-1/4 bg-gray-100 p-4 border-r border-gray-300">
        <h2 class="text-lg font-semibold mb-4">Acciones</h2>
        <div class="flex flex-col gap-4 mb-6">
            <a href="{{ route('reportes-veeam.dia') }}" class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 text-center">
                Reportes del día
            </a>
            <a href="{{ route('reportes-veeam.pendientes') }}" class="w-full bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 text-center">
                Reportes Pendientes
            </a>
        </div>

        <h2 class="text-lg font-semibold mb-3">Filtros</h2>
        <form action="{{ route('reportes-veeam.filtrados') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Fecha Inicio</label>
                <input type="date" name="fecha_desde" class="w-full border border-gray-300 rounded p-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Fecha Fin</label>
                <input type="date" name="fecha_hasta" class="w-full border border-gray-300 rounded p-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Estado</label>
                <select name="filtro_estado" class="w-full border border-gray-300 rounded p-2">
                    <option value="">Todos</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->nombre }}">{{ $estado->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Estado Ticket</label>
                <select name="filtro_estado_ticket" class="w-full border border-gray-300 rounded p-2">
                    <option value="">Todos</option>
                    @foreach($estadosTicket as $estadoTicket)
                        <option value="{{ $estadoTicket->nombre }}">{{ $estadoTicket->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Filtrar
            </button>
        </form>
    </aside>

    {{-- Formulario --}}
    <div class="flex-1 pl-8">
        <h1 class="text-3xl font-bold text-red-600 text-center mb-10">
            {{ $reporte ? '✏️ Editar Reporte Veeam' : '🔴 Nuevo Reporte Veeam' }}
        </h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form 
            action="{{ $reporte ? route('reportes-veeam.update', $reporte->id) : route('reportes-veeam.store') }}" 
            method="POST" 
            class="grid grid-cols-2 gap-6"
        >
            @csrf
            @if($reporte)
                @method('PUT')
            @endif

            {{-- Número de Ticket --}}
            <label class="uppercase text-base font-semibold text-gray-700">Número de Ticket</label>
            <div>
                <input type="text" name="numero_ticket"
                    value="{{ old('numero_ticket', $reporte->numero_ticket ?? 'N/A') }}"
                    class="p-2 border border-gray-300 rounded w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                @error('numero_ticket')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Fecha Inicio --}}
            <label class="uppercase text-base font-semibold text-gray-700">Fecha Inicio</label>
            <div>
                <input type="date" name="fecha_inicio" id="fecha_inicio"
                    value="{{ old('fecha_inicio', $reporte->fecha_inicio ?? now()->format('Y-m-d')) }}"
                    class="p-2 border border-gray-300 rounded w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                    required>
                @error('fecha_inicio')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Fecha Fin --}}
            <label class="uppercase text-base font-semibold text-gray-700">Fecha Fin</label>
            <div>
                <input type="date" name="fecha_fin" id="fecha_fin"
                    value="{{ old('fecha_fin', $reporte->fecha_fin ?? '') }}"
                    class="p-2 border border-gray-300 rounded w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                <small class="text-gray-500">Se llena automáticamente según el estado</small>
                @error('fecha_fin')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Estado --}}
            <label class="uppercase text-base font-semibold text-gray-700">Estado</label>
            <div>
                <select name="estado" id="estado" class="searchable-create w-full" required>
                    <option value="" disabled {{ !$reporte ? 'selected' : '' }}>Seleccione un estado</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->nombre }}" @selected(old('estado', $reporte->estado ?? '') == $estado->nombre)>
                            {{ $estado->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('estado')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Job Fallido --}}
            <label class="uppercase text-base font-semibold text-gray-700">Job Fallido</label>
            <div>
                <select name="job_fallido" id="job_fallido" class="searchable-create w-full" required>
                    <option value="" disabled {{ !$reporte ? 'selected' : '' }}>Seleccione un job</option>
                    @foreach($jobsFallidos as $job)
                        <option value="{{ $job->nombre }}" @selected(old('job_fallido', $reporte->job_fallido ?? '') == $job->nombre)>
                            {{ $job->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('job_fallido')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Seguimiento --}}
            <label class="uppercase text-base font-semibold text-gray-700 col-span-2">
                Seguimiento {{ $reporte && $reporte->estado === 'Failed' ? '(Editable)' : '' }}
            </label>

            @if($reporte && $reporte->seguimiento)
                {{-- Mostrar seguimientos anteriores en modo edición --}}
                <div class="col-span-2">
                    <textarea name="seguimiento" id="seguimiento"
                        class="p-2 border border-gray-300 rounded w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500" 
                        rows="6">{{ old('seguimiento', $reporte->seguimiento) }}</textarea>
                    <small class="text-gray-500">Puedes editar el texto completo arriba</small>
                </div>
                
                @if($reporte->estado === 'Failed')
                    {{-- Campo para agregar nuevo seguimiento solo en Failed --}}
                    <div class="col-span-2 mt-4 border-t pt-4">
                        <label class="block text-base font-semibold text-green-700 mb-2">
                            ➕ Agregar Nuevo Seguimiento
                        </label>
                        <textarea name="seguimiento_nuevo" id="seguimiento_nuevo" 
                            placeholder="Escribe aquí el nuevo seguimiento (se agregará con la fecha actual)"
                            class="p-2 border border-green-300 rounded w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500" 
                            rows="3">{{ old('seguimiento_nuevo', '') }}</textarea>
                        <small class="text-gray-500">Este se agregará debajo de los anteriores con la fecha actual</small>
                    </div>
                @endif
            @else
                {{-- Modo creación --}}
                <textarea name="seguimiento" id="seguimiento" placeholder="Ej: Se envía un correo de carácter informativo"
                    class="p-2 border border-gray-300 rounded w-full col-span-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500" 
                    rows="3">{{ old('seguimiento', '') }}</textarea>
            @endif

            @error('seguimiento')
                <span class="text-red-500 text-sm col-span-2">{{ $message }}</span>
            @enderror
            @error('seguimiento_nuevo')
                <span class="text-red-500 text-sm col-span-2">{{ $message }}</span>
            @enderror

            {{-- Descripción del Error --}}
            <label class="uppercase text-base font-semibold text-gray-700 col-span-2">Descripción del Error</label>
            <textarea name="descripcion_error" placeholder="Descripción detallada del error"
                class="p-2 border border-gray-300 rounded w-full col-span-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500" 
                rows="4" required>{{ old('descripcion_error', $reporte->descripcion_error ?? '') }}</textarea>
            @error('descripcion_error')
                <span class="text-red-500 text-sm col-span-2">{{ $message }}</span>
            @enderror

            {{-- Estado Ticket --}}
            <label class="uppercase text-base font-semibold text-gray-700">Estado Ticket</label>
            <div>
                <select name="estado_ticket" id="estado_ticket" class="searchable-create w-full" required>
                    <option value="" disabled>Seleccione estado del ticket</option>
                    @foreach($estadosTicket as $estadoTicket)
                        <option value="{{ $estadoTicket->nombre }}" @selected(old('estado_ticket', $reporte->estado_ticket ?? '') == $estadoTicket->nombre)>
                            {{ $estadoTicket->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('estado_ticket')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Botones --}}
            <div class="col-span-2 flex justify-between items-center mt-4">
                <a href="{{ route('menu.analista') }}"
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded font-medium shadow transition">
                    Menú
                </a>
                @if(!$reporte)
                <button type="button"
                    onclick="pedirTicketEditar()"
                    class="bg-yellow-400 hover:bg-yellow-500 text-white px-6 py-2 rounded font-medium shadow transition mx-2">
                    Editar Reporte
                </button>
                @endif
                <button type="submit"
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded font-medium shadow transition">
                    {{ $reporte ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fechaFinInput = document.getElementById('fecha_fin');
        const fechaInicioInput = document.getElementById('fecha_inicio');
        const seguimientoTextarea = document.getElementById('seguimiento');
        
        const esEdicion = {{ $reporte ? 'true' : 'false' }};

        function getFechaActual() {
            const hoy = new Date();
            const dia = String(hoy.getDate()).padStart(2, '0');
            const mes = String(hoy.getMonth() + 1).padStart(2, '0');
            const anio = hoy.getFullYear();
            return `${dia}-${mes}-${anio}`;
        }

        const estadoSelectInstance = new TomSelect('#estado', {
            create: true,
            persist: false,
            maxOptions: 200,
            sortField: { field: "text", direction: "asc" },
            placeholder: 'Buscar o escribir nuevo...',
            createOnBlur: true,
            onChange: function(value) {
                const fechaActual = getFechaActual();

                if (value === 'Warning') {
                    fechaFinInput.value = fechaInicioInput.value;
                    estadoTicketSelectInstance.setValue('Informativo');
                    
                    if (!esEdicion && !seguimientoTextarea.value.trim()) {
                        seguimientoTextarea.value = `${fechaActual} Se envía correo de carácter informativo`;
                    }
                    
                    if (!esEdicion) {
                        seguimientoTextarea.readOnly = true;
                        seguimientoTextarea.classList.add('bg-gray-100', 'cursor-not-allowed');
                    }
                    
                } else if (value === 'Failed') {
                    fechaFinInput.value = '';
                    estadoTicketSelectInstance.setValue('Pendiente');
                    
                    if (!esEdicion && !seguimientoTextarea.value.trim()) {
                        seguimientoTextarea.value = `${fechaActual} Se envía correo a Emtec para su información y reprocesamiento`;
                    }
                    
                    seguimientoTextarea.readOnly = false;
                    seguimientoTextarea.classList.remove('bg-gray-100', 'cursor-not-allowed');
                    
                } else {
                    seguimientoTextarea.readOnly = false;
                    seguimientoTextarea.classList.remove('bg-gray-100', 'cursor-not-allowed');
                }
            },
            onItemAdd: function(value, item) {
                guardarNuevoValor('estado', value);
            }
        });

        const jobFallidoSelectInstance = new TomSelect('#job_fallido', {
            create: true,
            persist: false,
            maxOptions: 200,
            sortField: { field: "text", direction: "asc" },
            placeholder: 'Buscar o escribir nuevo...',
            createOnBlur: true,
            onItemAdd: function(value, item) {
                guardarNuevoValor('job_fallido', value);
            }
        });

        const estadoTicketSelectInstance = new TomSelect('#estado_ticket', {
            create: true,
            persist: false,
            maxOptions: 200,
            sortField: { field: "text", direction: "asc" },
            placeholder: 'Buscar o escribir nuevo...',
            createOnBlur: true,
            onChange: function(value) {
                if (value === 'Resuelto' && !fechaFinInput.value) {
                    fechaFinInput.value = new Date().toISOString().split('T')[0];
                }
            },
            onItemAdd: function(value, item) {
                guardarNuevoValor('estado_ticket', value);
            }
        });

        fechaInicioInput.addEventListener('change', function () {
            if (estadoSelectInstance.getValue() === 'Warning') {
                fechaFinInput.value = this.value;
            }
        });

        if (!esEdicion && estadoSelectInstance.getValue() === 'Warning') {
            seguimientoTextarea.readOnly = true;
            seguimientoTextarea.classList.add('bg-gray-100', 'cursor-not-allowed');
        }
    });

    function guardarNuevoValor(campo, valor) {
        let url = '';
        
        if (campo === 'estado') url = '{{ route("veeam.guardar-estado") }}';
        else if (campo === 'job_fallido') url = '{{ route("veeam.guardar-job") }}';
        else if (campo === 'estado_ticket') url = '{{ route("veeam.guardar-estado-ticket") }}';

        if (url) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ nombre: valor })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) console.log('✅ Guardado:', valor);
            })
            .catch(error => console.error('Error:', error));
        }
    }

    function pedirTicketEditar() {
        const id = prompt('Ingrese el ID del reporte a editar:');
        if (id && id.trim() !== '') {
            window.location.href = '/reportes-veeam/' + encodeURIComponent(id.trim()) + '/edit';
        }
    }
</script>
</body>
</html>