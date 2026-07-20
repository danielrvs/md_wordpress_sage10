{{-- Nonce generado en PHP y pasado como variable para evitar output directo --}}
{!! $nonceField !!}

<div style="padding: 10px 0;">
    <table class="wp-list-table widefat fixed striped" style="margin-bottom: 20px; border: 1px solid #ccd0d4;">
        <thead>
            <tr>
                <th style="padding: 10px;"><b>Fecha Inicio</b></th>
                <th style="padding: 10px;"><b>Fecha Fin</b></th>
                <th style="padding: 10px;"><b>Motivo / Descripción</b></th>
                <th style="padding: 10px; width: 100px;"><b>Acciones</b></th>
            </tr>
        </thead>
        <tbody>
            @forelse($absences as $absence)
                <tr>
                    <td style="padding: 10px;"><code>{{ $absence['start_date'] }}</code></td>
                    <td style="padding: 10px;"><code>{{ $absence['end_date'] }}</code></td>
                    <td style="padding: 10px;">{{ $absence['reason'] ?: 'No especificado' }}</td>
                    <td style="padding: 10px;">
                        <a href="{{ esc_url(wp_nonce_url(admin_url("post.php?post={$postId}&action=edit&remove_absence={$absence['id']}"), 'remove_abs_' . $absence['id'])) }}"
                            class="button button-link-delete"
                            style="color: #b32d2e; text-decoration: none;">Eliminar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="color:#666; font-style:italic; padding: 10px;">
                        Este médico no tiene periodos de vacaciones programados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h4 style="border-top: 1px solid #ddd; padding-top: 15px; margin-bottom: 15px;">
        ✨ Añadir Nuevo Periodo de Ausencia
    </h4>
    <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        <div>
            <label style="display:block; margin-bottom:5px; font-weight: 600;">Inicio:</label>
            <input type="date" name="new_abs_start" style="height: 30px; padding: 4px;">
        </div>
        <div>
            <label style="display:block; margin-bottom:5px; font-weight: 600;">Fin:</label>
            <input type="date" name="new_abs_end" style="height: 30px; padding: 4px;">
        </div>
        <div style="flex-grow: 1; min-width: 200px;">
            <label style="display:block; margin-bottom:5px; font-weight: 600;">Motivo:</label>
            <input type="text" name="new_abs_reason" placeholder="Ej: Vacaciones de Verano"
                style="width: 100%; height: 30px;">
        </div>
        <div>
            <input type="submit" name="save_absence_btn" class="button button-primary" value="Bloquear Calendario">
        </div>
    </div>
</div>
