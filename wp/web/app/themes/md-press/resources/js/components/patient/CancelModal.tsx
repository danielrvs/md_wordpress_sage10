import { __ } from '../../utils/i18n';

interface AppointmentData {
  id: number;
  doctor_name: string;
  doctor_specialty: string;
  appointment_date: string;
}

interface CancelModalProps {
  appointment: AppointmentData | null;
  isOpen: boolean;
  onClose: () => void;
  onConfirm: (appointmentId: number) => Promise<void>;
  submitting: boolean;
}

export function CancelModal({ appointment, isOpen, onClose, onConfirm, submitting }: CancelModalProps) {
  if (!isOpen || !appointment) return null;

  const formatDate = (dateStr: string) => {
    try {
      const dateObj = new Date(dateStr.replace(' ', 'T'));
      return dateObj.toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    } catch (e) {
      return dateStr;
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-fade-in font-sans">
      <div 
        className="absolute inset-0" 
        onClick={() => {
          if (!submitting) onClose();
        }} 
      />

      <div className="relative w-full max-w-md bg-slate-900 border border-white/15 rounded-3xl p-6 sm:p-8 shadow-2xl shadow-red-500/10 space-y-6 z-10">
        {/* Header */}
        <div className="flex items-start justify-between gap-4 border-b border-white/10 pb-4">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center justify-center shrink-0">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div>
              <h3 className="text-lg font-bold text-white">
                {__('portal.cancel_modal_title', '¿Cancelar Cita Médica?')}
              </h3>
              <p className="text-xs text-slate-400">
                {__('portal.cancel_modal_sub', 'Esta acción no se puede deshacer.')}
              </p>
            </div>
          </div>

          <button
            type="button"
            disabled={submitting}
            onClick={onClose}
            className="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-white/5 transition-colors disabled:opacity-50"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        {/* Appointment details summary */}
        <div className="bg-white/5 border border-white/10 rounded-2xl p-4 space-y-2 text-xs">
          <div className="flex justify-between">
            <span className="text-slate-400">Especialista:</span>
            <span className="text-white font-bold">{appointment.doctor_name}</span>
          </div>
          <div className="flex justify-between">
            <span className="text-slate-400">Especialidad:</span>
            <span className="text-emerald-400 font-semibold">{appointment.doctor_specialty}</span>
          </div>
          <div className="flex justify-between border-t border-white/5 pt-2 mt-2">
            <span className="text-slate-400">Fecha y Hora:</span>
            <span className="text-white font-bold capitalize">{formatDate(appointment.appointment_date)}</span>
          </div>
        </div>

        {/* Actions */}
        <div className="flex items-center justify-end gap-3 pt-2">
          <button
            type="button"
            disabled={submitting}
            onClick={onClose}
            className="px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-slate-300 hover:text-white hover:bg-white/10 text-xs font-semibold transition-all disabled:opacity-50 cursor-pointer"
          >
            {__('portal.cancel_modal_keep', 'Mantener Cita')}
          </button>

          <button
            type="button"
            disabled={submitting}
            onClick={() => onConfirm(appointment.id)}
            className="px-5 py-2.5 rounded-xl bg-red-500/20 border border-red-500/40 text-red-300 hover:bg-red-500 hover:text-white text-xs font-bold transition-all shadow-md active:scale-95 disabled:opacity-50 cursor-pointer flex items-center gap-2"
          >
            {submitting && (
              <svg className="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            )}
            {__('portal.cancel_modal_confirm', 'Sí, Cancelar Cita')}
          </button>
        </div>
      </div>
    </div>
  );
}
