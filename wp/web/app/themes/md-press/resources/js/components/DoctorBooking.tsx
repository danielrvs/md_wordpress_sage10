import { useState } from '@wordpress/element';
import { Button } from './Button';

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

  const handleDateChange = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const newDate = e.target.value;
    if (!newDate) return;

    setDate(newDate);
    setLoading(true);
    setSelectedSlot(null);
    setBookingSuccess(false);

    try {
      const response = await fetch(`/wp-json/api/v1/doctors/${doctorId}/schedule?date=${newDate}`);
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

  const handleBookSlot = () => {
    if (!selectedSlot) return;
    setBookingSuccess(true);
  };

  // Formato legible de fecha
  const formatFriendlyDate = (dateStr: string) => {
    try {
      const options: Intl.DateTimeFormatOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      const dateObj = new Date(dateStr + 'T00:00:00');
      return dateObj.toLocaleDateString('es-ES', options);
    } catch (e) {
      return dateStr;
    }
  };

  return (
    <div className="space-y-6">
      {/* Date Picker Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-xl">
        <div>
          <h3 className="text-base font-bold text-white uppercase tracking-wider text-slate-400">Seleccionar Fecha</h3>
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
          <p className="text-xs font-semibold">Cargando horarios disponibles...</p>
        </div>
      )}

      {/* Slots display */}
      {!loading && (
        <>
          {schedule && schedule.isWorkday && schedule.slots.length > 0 ? (
            <div className="space-y-6">
              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                {schedule.slots.map((slot, index) => {
                  const isSelected = selectedSlot?.start_time === slot.start_time;
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
                        {slot.type}
                      </span>
                    </button>
                  );
                })}
              </div>

              {/* Selection Confirmation Block */}
              {selectedSlot && (
                <div className="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl animate-fade-in flex flex-col md:flex-row md:items-center justify-between gap-4">
                  <div>
                    <h4 className="text-sm font-bold text-white">Reserva de Turno</h4>
                    <p className="text-xs text-slate-400 mt-1">
                      Has seleccionado una cita <span className="font-semibold text-emerald-400 uppercase text-[10px]">{selectedSlot.type}</span> para el <span className="text-white font-medium">{formatFriendlyDate(date)}</span> a las <span className="text-white font-semibold">{selectedSlot.start_time} hs</span>.
                    </p>
                  </div>

                  {bookingSuccess ? (
                    <div className="bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 rounded-xl px-4 py-2 text-xs font-bold flex items-center gap-2">
                      <svg className="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                      </svg>
                      ¡Cita Reservada con Éxito!
                    </div>
                  ) : (
                    <Button
                      onClick={handleBookSlot}
                      variant="primary"
                      className="py-2.5 px-5 text-xs shrink-0"
                    >
                      Confirmar Reserva
                    </Button>
                  )}
                </div>
              )}
            </div>
          ) : (
            <div className="bg-amber-500/10 border border-amber-500/20 text-amber-300 rounded-2xl p-6 flex items-start gap-3.5">
              <svg className="w-5.5 h-5.5 shrink-0 text-amber-400 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <div>
                <h4 className="text-sm font-bold text-white">Sin consultas disponibles</h4>
                <p className="text-xs text-slate-400 mt-1 leading-relaxed">
                  Este especialista no tiene horarios programados o se encuentra ausente (vacaciones, congresos) para la fecha seleccionada. Por favor, selecciona otro día en el calendario.
                </p>
              </div>
            </div>
          )}
        </>
      )}
    </div>
  );
}
