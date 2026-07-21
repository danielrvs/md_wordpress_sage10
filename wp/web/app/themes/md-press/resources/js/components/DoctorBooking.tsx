import { useState, useEffect } from '@wordpress/element';
import { Button } from './Button';
import { __, getLocale } from '../utils/i18n';

interface Slot {
  start_time: string;
  end_time: string;
  is_available: boolean;
  type: 'presencial' | 'telemedicina';
}

interface Schedule {
  doctorId: number;
  date: string;
  isWorkday: boolean;
  slots: Slot[];
}

interface DoctorBookingProps {
  doctorId: number;
  initialDate: string;
  initialSchedule: Schedule | null;
}

export function DoctorBooking({ doctorId, initialDate, initialSchedule }: DoctorBookingProps) {
  const [date, setDate] = useState<string>(initialDate);
  const [schedule, setSchedule] = useState<Schedule | null>(initialSchedule);
  const [loading, setLoading] = useState<boolean>(false);
  const [selectedSlot, setSelectedSlot] = useState<Slot | null>(null);
  const [bookingSuccess, setBookingSuccess] = useState<boolean>(false);
  const [submitting, setSubmitting] = useState<boolean>(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [isUnauthorized, setIsUnauthorized] = useState<boolean>(false);

  const fetchSchedule = async (targetDate: string) => {
    setLoading(true);
    setSelectedSlot(null);
    setBookingSuccess(false);
    setErrorMessage(null);
    setIsUnauthorized(false);

    try {
      const locale = getLocale();
      const response = await fetch(`/wp-json/api/v1/doctors/${doctorId}/schedule?date=${targetDate}&lang=${locale}`);
      if (response.ok) {
        const data: Schedule = await response.json();
        setSchedule(data);
      } else {
        setSchedule(null);
      }
    } catch (error) {
      console.error('Error fetching schedule:', error);
      setSchedule(null);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchSchedule(date);
  }, [date]);

  const handleDateChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const newDate = e.target.value;
    if (newDate) {
      setDate(newDate);
    }
  };

  const handleBookSlot = async () => {
    if (!selectedSlot || submitting) return;
    setSubmitting(true);
    setErrorMessage(null);

    try {
      const response = await fetch('/wp-json/api/v1/appointments', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          doctor_id: doctorId,
          patient_id: 1,
          clinic_id: 1,
          appointment_date: date,
          start_time: selectedSlot.start_time,
          notes: 'Reserva web',
        }),
      });

      const data = await response.json();

      if (response.ok && data.success) {
        setBookingSuccess(true);
        // Refresh schedule after 1.5s so the booked slot is updated in UI
        setTimeout(() => {
          fetchSchedule(date);
        }, 1500);
      } else {
        if (response.status === 401) {
          setIsUnauthorized(true);
        }
        const errorText = data.message || __('booking.error', 'No se pudo completar la reserva.');
        setErrorMessage(errorText);
      }
    } catch (error) {
      console.error('Error booking appointment:', error);
      setErrorMessage(__('booking.error_network', 'Error de conexión al procesar la reserva.'));
    } finally {
      setSubmitting(false);
    }
  };

  const formatFriendlyDate = (dateStr: string) => {
    try {
      const options: Intl.DateTimeFormatOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      const dateObj = new Date(dateStr + 'T00:00:00');
      const localeCode = getLocale() === 'en' ? 'en-US' : 'es-ES';
      return dateObj.toLocaleDateString(localeCode, options);
    } catch (e) {
      return dateStr;
    }
  };

  return (
    <div className="space-y-6">
      {/* Date Picker Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-xl">
        <div>
          <h3 className="text-base font-bold text-white uppercase tracking-wider text-slate-400">
            {__('booking.select_date', 'Seleccionar Fecha')}
          </h3>
          <p className="text-sm text-emerald-400 mt-1 capitalize font-medium">
            {formatFriendlyDate(date)}
          </p>
        </div>
        
        <div className="relative">
          <input
            type="date"
            value={date}
            onChange={handleDateChange}
            min={new Date().toISOString().split('T')[0]}
            className="w-full sm:w-auto bg-slate-900/60 border border-white/10 hover:border-emerald-500/30 text-white font-medium px-4 py-2.5 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all cursor-pointer text-sm"
          />
        </div>
      </div>

      {/* Loading state */}
      {loading && (
        <div className="flex flex-col items-center justify-center py-12 text-slate-400">
          <svg className="animate-spin h-8 w-8 text-emerald-500 mb-3" fill="none" viewBox="0 0 24 24">
            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p className="text-xs font-semibold">{__('booking.loading', 'Cargando horarios disponibles...')}</p>
        </div>
      )}

      {/* Slots display */}
      {!loading && (
        <>
          {schedule && schedule.isWorkday && schedule.slots.filter(s => s.is_available !== false).length > 0 ? (
            <div className="space-y-6">
              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                {schedule.slots.filter(s => s.is_available !== false).map((slot, index) => {
                  const isSelected = selectedSlot?.start_time === slot.start_time;
                  const typeLabel = slot.type === 'telemedicina' 
                    ? __('booking.telemedicina', 'Telemedicina') 
                    : __('booking.presencial', 'Presencial');

                  return (
                    <button
                      key={index}
                      onClick={() => {
                        if (!bookingSuccess) setSelectedSlot(slot);
                      }}
                      className={`relative p-3.5 rounded-xl border text-center transition-all cursor-pointer active:scale-95 flex flex-col items-center justify-center gap-1.5 ${
                        isSelected
                          ? 'border-emerald-400 bg-emerald-500/15 ring-2 ring-emerald-500/25'
                          : 'border-white/10 bg-slate-900/40 hover:border-emerald-500/30 hover:bg-emerald-500/5'
                      }`}
                    >
                      <div className="text-sm font-bold text-white">
                        {slot.start_time} - {slot.end_time}
                      </div>
                      
                      <span className={`text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider shrink-0 ${
                        slot.type === 'telemedicina'
                          ? 'text-cyan-400 bg-cyan-500/10 border border-cyan-500/20'
                          : 'text-emerald-400 bg-emerald-500/10 border border-emerald-500/20'
                      }`}>
                        {typeLabel}
                      </span>
                    </button>
                  );
                })}
              </div>

              {/* Confirmation Modal Popup Overlay */}
              {selectedSlot && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-fade-in">
                  {/* Backdrop overlay click to close */}
                  <div 
                    className="absolute inset-0" 
                    onClick={() => {
                      if (!submitting) {
                        setSelectedSlot(null);
                        setErrorMessage(null);
                      }
                    }}
                  />

                  <div className="relative w-full max-w-md bg-slate-900 border border-white/15 rounded-3xl p-6 sm:p-8 shadow-2xl shadow-emerald-500/10 space-y-6 z-10">
                    {/* Header */}
                    <div className="flex items-start justify-between gap-4 border-b border-white/10 pb-4">
                      <div>
                        <span className="text-[10px] font-bold text-emerald-400 uppercase tracking-widest bg-emerald-500/10 px-2.5 py-1 rounded-full border border-emerald-500/20">
                          {__('booking.modal_tag', 'Confirmación de Reserva')}
                        </span>
                        <h3 className="text-xl font-extrabold text-white mt-2">
                          {__('booking.modal_title', 'Reserva de Cita Médica')}
                        </h3>
                      </div>
                      <button
                        type="button"
                        disabled={submitting}
                        onClick={() => {
                          setSelectedSlot(null);
                          setErrorMessage(null);
                        }}
                        className="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-white/5 transition-colors disabled:opacity-50"
                      >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>

                    {/* Error Message Alert */}
                    {errorMessage && (
                      <div className="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold space-y-3">
                        <div className="flex items-center gap-2">
                          <svg className="w-4 h-4 shrink-0" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                          </svg>
                          <span>{errorMessage}</span>
                        </div>
                        {isUnauthorized && (
                          <a
                            href="/auth/login"
                            className="inline-flex items-center justify-center w-full py-2 px-3 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs transition-colors shadow-md"
                          >
                            {__('nav.login', 'Iniciar Sesión')}
                          </a>
                        )}
                      </div>
                    )}

                    {/* Success State */}
                    {bookingSuccess ? (
                      <div className="py-8 text-center space-y-4">
                        <div className="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 mx-auto flex items-center justify-center animate-bounce">
                          <svg className="w-8 h-8" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                          </svg>
                        </div>
                        <h4 className="text-lg font-bold text-white">
                          {__('booking.success', '¡Cita Reservada con Éxito!')}
                        </h4>
                        <p className="text-xs text-slate-400">
                          {formatFriendlyDate(date)} - <span className="text-emerald-400 font-bold">{selectedSlot.start_time} hs</span>
                        </p>
                      </div>
                    ) : (
                      /* Booking Details & Action Buttons */
                      <div className="space-y-6">
                        <div className="bg-white/5 border border-white/10 rounded-2xl p-4 space-y-3">
                          <div className="flex items-center justify-between text-sm">
                            <span className="text-slate-400">{__('booking.date_label', 'Fecha:')}</span>
                            <span className="text-white font-bold capitalize">{formatFriendlyDate(date)}</span>
                          </div>
                          <div className="flex items-center justify-between text-sm border-t border-white/5 pt-2.5">
                            <span className="text-slate-400">{__('booking.time_label', 'Horario:')}</span>
                            <span className="text-emerald-400 font-extrabold">{selectedSlot.start_time} - {selectedSlot.end_time} hs</span>
                          </div>
                          <div className="flex items-center justify-between text-sm border-t border-white/5 pt-2.5">
                            <span className="text-slate-400">{__('booking.type_label', 'Modalidad:')}</span>
                            <span className={`text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider ${
                              selectedSlot.type === 'telemedicina'
                                ? 'text-cyan-400 bg-cyan-500/10 border border-cyan-500/20'
                                : 'text-emerald-400 bg-emerald-500/10 border border-emerald-500/20'
                            }`}>
                              {selectedSlot.type === 'telemedicina'
                                ? __('booking.telemedicina', 'Telemedicina')
                                : __('booking.presencial', 'Presencial')}
                            </span>
                          </div>
                        </div>

                        <div className="flex items-center justify-end gap-3 pt-2">
                          <Button
                            type="button"
                            variant="secondary"
                            disabled={submitting}
                            onClick={() => {
                              setSelectedSlot(null);
                              setErrorMessage(null);
                            }}
                            className="py-2.5 px-4 text-xs"
                          >
                            {__('booking.btn_cancel', 'Cancelar')}
                          </Button>
                          <Button
                            type="button"
                            variant="primary"
                            disabled={submitting}
                            onClick={handleBookSlot}
                            className="py-2.5 px-5 text-xs flex items-center gap-2"
                          >
                            {submitting && (
                              <svg className="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                              </svg>
                            )}
                            {__('booking.confirm', 'Confirmar Reserva')}
                          </Button>
                        </div>
                      </div>
                    )}
                  </div>
                </div>
              )}
            </div>
          ) : (
            <div className="bg-amber-500/10 border border-amber-500/20 text-amber-300 rounded-2xl p-6 flex items-start gap-3.5">
              <svg className="w-5.5 h-5.5 shrink-0 text-amber-400 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <div>
                <h4 className="text-sm font-bold text-white">{__('booking.no_slots', 'Sin consultas disponibles')}</h4>
              </div>
            </div>
          )}
        </>
      )}
    </div>
  );
}
