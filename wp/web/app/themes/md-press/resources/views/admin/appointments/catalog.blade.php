<div class="wrap">
    <h1 class="wp-heading-inline">Control Central de Citas</h1>
    <p class="description">Selecciona un médico de la lista para gestionar sus próximas consultas programadas.</p>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped pages" style="margin-top: 20px;">
        <thead>
            <tr>
                <th style="padding: 12px; font-weight: bold;">Médico</th>
                <th style="padding: 12px; font-weight: bold; width: 180px;">Próximas Citas</th>
                <th style="padding: 12px; font-weight: bold; width: 120px; text-align: right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($doctors as $doctor)
                @php
                    $count = count($service->execute($doctor->ID));
                    $agendaUrl = admin_url('admin.php?page=' . $menuSlug . '&doctor_id=' . $doctor->ID);
                @endphp
                <tr>
                    <td style="padding: 12px; font-size: 14px;">
                        <strong>
                            <a href="{{ $agendaUrl }}" class="row-title">
                                👤 {{ $doctor->post_title }}
                            </a>
                        </strong>
                    </td>
                    <td style="padding: 12px;">
                        <span class="badge" style="background: #e1ecf4; color: #0c4b78; padding: 4px 8px; border-radius: 12px; font-weight: 600; font-size: 12px;">
                            {{ $count }} citas
                        </span>
                    </td>
                    <td style="padding: 12px; text-align: right;">
                        <a href="{{ $agendaUrl }}" class="button button-primary button-small">
                            Ver Agenda
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 20px; color: #666;">
                        No se encontraron médicos publicados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
