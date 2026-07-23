import { __ } from '../../utils/i18n';

export interface Appointment {
  id: number;
  doctor_id: number;
  doctor_name: string;
  doctor_specialty: string;
  doctor_location: string;
  doctor_avatar: string | null;
  appointment_date: string;
  status: 'confirmed' | 'cancelled' | 'completed' | string;
  notes: string | null;
  created_at: string;
}

interface AppointmentCardProps {
  appointment: Appointment;
  onCancelRequest: (appointment: Appointment) => void;
}

export function AppointmentCard({ appointment, onCancelRequest }: AppointmentCardProps) {
  const isCancelled = appointment.status === 'cancelled';
  const isCompleted = appointment.status === 'completed';
  
  const dateObj = new Date(appointment.appointment_date.replace(' ', 'T'));
  const isUpcoming = dateObj.getTime() > Date.now() && !isCancelled && !isCompleted;

  const formattedDate = dateObj.toLocaleDateString('es-ES', {
    weekday: 'short',
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  });

  const formattedTime = dateObj.toLocaleTimeString('es-ES', {
    hour: '2-digit',
    minute: '2-digit',
  });

  return (
    <div className={`relative flex flex-col justify-between rounded-3xl border transition-all duration-300 p-6 shadow-xl backdrop-blur-xl ${
      isCancelled
        ? 'bg-slate-900/30 border-white/5 opacity-70'
        : isUpcoming
        ? 'bg-white/5 border-white/10 hover:border-emerald-500/40 hover:-translate-y-1 shadow-emerald-500/5'
        : 'bg-white/5 border-white/10'
    }`}>
      {/* Top Header: Doctor Info + Status Badge */}
      <div>
        <div className="flex items-start justify-between gap-4 mb-4">
          <div className="flex items-center gap-3.5">
            {/* Doctor Avatar / Fallback Icon */}
            <div className="w-12 h-12 rounded-2xl overflow-hidden bg-slate-800 border border-white/10 shrink-0 flex items-center justify-center text-emerald-400 font-extrabold text-base shadow-inner">
              {appointment.doctor_avatar ? (
                <img src={appointment.doctor_avatar} alt={appointment.doctor_name} className="w-full h-full object-cover" />
              ) : (
                <span>{appointment.doctor_name.charAt(0)}</span>
              )}
            </div>

            <div>
              <h4 className="text-base font-bold text-white hover:text-emerald-300 transition-colors">
                <a href={`/doctors/`}>{appointment.doctor_name}</a>
              </h4>
              <span className="inline-block text-xs font-semibold text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20 mt-0.5">
                {appointment.doctor_specialty}
              </span>
            </div>
          </div>

          {/* Status Badge */}
          <span className={`text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider border shrink-0 ${
            isCancelled
              ? 'text-red-400 bg-red-500/10 border-red-500/20'
              : isCompleted
              ? 'text-slate-400 bg-slate-500/10 border-slate-500/20'
              : 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30 animate-pulse'
          }`}>
            {isCancelled
              ? __('portal.status_cancelled', 'Cancelada')
              : isCompleted
              ? __('portal.status_completed', 'Completada')
              : __('portal.status_upcoming', 'Confirmada')}
          </span>
        </div>

        {/* Date & Time Badge Row */}
        <div className="grid grid-cols-2 gap-3 my-4 p-3.5 rounded-2xl bg-slate-900/60 border border-white/5 text-xs">
          <div className="flex items-center gap-2 text-slate-300">
            <svg className="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
            <span className="capitalize font-semibold">{formattedDate}</span>
          </div>

          <div className="flex items-center gap-2 text-slate-300">
            <svg className="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span className="font-bold text-white">{formattedTime} hs</span>
          </div>
        </div>

        {/* Location & Notes */}
        {appointment.doctor_location && (
          <div className="flex items-center gap-2 text-xs text-slate-400 mt-2">
            <svg className="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
              <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
            </svg>
            <span className="truncate">{appointment.doctor_location}</span>
          </div>
        )}
      </div>

      {/* Action Footer */}
      <div className="mt-6 pt-4 border-t border-white/5 flex items-center justify-between gap-3">
        <a
          href={`/doctors/`}
          className="text-xs font-semibold text-slate-400 hover:text-emerald-400 transition-colors flex items-center gap-1"
        >
          {__('portal.view_doctor', 'Ver especialista')}
          <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
          </svg>
        </a>

        {isUpcoming && (
          <button
            type="button"
            onClick={() => onCancelRequest(appointment)}
            className="px-3.5 py-1.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 hover:text-red-300 text-xs font-bold transition-all active:scale-95 cursor-pointer"
          >
            {__('portal.cancel_btn', 'Cancelar cita')}
          </button>
        )}
      </div>
    </div>
  );
}
