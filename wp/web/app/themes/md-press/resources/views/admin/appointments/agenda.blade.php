<div class="wrap">
    <a href="{{ $backUrl }}" class="page-title-action" style="margin-right: 8px;">← Volver al listado</a>
    <h1 class="wp-heading-inline">Agenda de: {{ $doctorName }}</h1>
    <hr class="wp-header-end">

    {{-- BARRA DE FILTROS --}}
    <div class="tablenav top" style="margin: 20px 0 10px 0; height: auto; padding: 10px; background: #fff; border: 1px solid #ccd0d4;">
        <form method="get" action="{{ admin_url('admin.php') }}" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <input type="hidden" name="page" value="{{ $menuSlug }}">
            <input type="hidden" name="doctor_id" value="{{ $doctorId }}">

            <div class="alignleft actions">
                <label for="filter_date" style="font-weight: 600; margin-right: 5px;">Filtrar por Día:</label>
                <input type="date" id="filter_date" name="filter_date" value="{{ $filterDate }}" style="height: 30px;">
            </div>

            <div class="alignleft actions">
                <label for="order_dir" style="font-weight: 600; margin-right: 5px;">Cronología:</label>
                <select id="order_dir" name="order_dir" style="height: 30px; min-width: 160px;">
                    <option value="asc" @selected($orderDir === 'ASC')>Más próximas primero</option>
                    <option value="desc" @selected($orderDir === 'DESC')>Más lejanas primero</option>
                </select>
            </div>

            <div class="alignleft actions">
                <input type="submit" class="button action button-secondary" value="Aplicar Filtros">
                @if($filterDate)
                    <a href="{{ $agendaBase }}" class="button button-link" style="color: #b32d2e; text-decoration: none; margin-left: 10px;">
                        Limpiar Filtro
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <table class="wp-list-table widefat fixed striped pages" style="border: 1px solid #ccd0d4;">
        <thead>
            <tr>
                <th style="padding: 12px; width: 80px;"><b>Cita ID</b></th>
                <th style="padding: 12px; width: 160px;"><b>Fecha y Hora</b></th>
                <th style="padding: 12px;"><b>Paciente</b></th>
                <th style="padding: 12px; width: 100px;"><b>Clínica ID</b></th>
                <th style="padding: 12px; width: 120px;"><b>Estado</b></th>
                <th style="padding: 12px;"><b>Notas de Consulta</b></th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appointment)
                @php
                    $formattedDate = (new DateTime($appointment->dateTime))->format('d/m/Y H:i');
                    $style = $statusColors[$appointment->status] ?? ['bg' => '#fff', 'text' => '#000', 'border' => '#ccc'];
                @endphp
                <tr>
                    <td style="padding: 12px;"><code>#{{ $appointment->id }}</code></td>
                    <td style="padding: 12px; font-size: 13px;"><b>🗓️ {{ $formattedDate }} hs</b></td>
                    <td style="padding: 12px;">👤 Paciente ID: <code>{{ $appointment->patientId }}</code></td>
                    <td style="padding: 12px;">🏥 ID: {{ $appointment->clinicId }}</td>
                    <td style="padding: 12px;">
                        <span style="background: {{ $style['bg'] }}; color: {{ $style['text'] }}; border: 1px solid {{ $style['border'] }}; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; display: inline-block;">
                            {{ $appointment->status }}
                        </span>
                    </td>
                    <td style="padding: 12px; color: #555; font-size: 12px; line-height: 1.4;">
                        {{ $appointment->notes ?: 'Sin observaciones' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="color:#666; font-style:italic; padding: 25px; text-align: center; font-size: 14px;">
                        {{ $filterDate ? 'No se encontraron citas para el día seleccionado.' : 'Este médico no tiene citas pendientes.' }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
