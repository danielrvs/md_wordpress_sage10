import { useState, useEffect } from '@wordpress/element';
import { Appointment, AppointmentCard } from './AppointmentCard';
import { CancelModal } from './CancelModal';
import { __ } from '../../utils/i18n';

export function PatientDashboard() {
  const [appointments, setAppointments] = useState<Appointment[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  
  // Tabs & Filters
  const [activeTab, setActiveTab] = useState<'upcoming' | 'past' | 'cancelled' | 'all'>('upcoming');
  const [searchQuery, setSearchQuery] = useState<string>('');

  // Cancel Modal state
  const [cancellingAppointment, setCancellingAppointment] = useState<Appointment | null>(null);
  const [submittingCancel, setSubmittingCancel] = useState<boolean>(false);
  const [toastMessage, setToastMessage] = useState<string | null>(null);

  const fetchAppointments = async () => {
    setLoading(true);
    setError(null);

    try {
      const appSettings = (window as any).AppTranslations;
      const headers: Record<string, string> = {
        'Content-Type': 'application/json',
      };
      if (appSettings?.nonce) {
        headers['X-WP-Nonce'] = appSettings.nonce;
      }

      const response = await fetch('/wp-json/api/v1/patient/appointments', {
        headers,
      });

      if (response.ok) {
        const data = await response.json();
        setAppointments(data.appointments || []);
      } else {
        const data = await response.json();
        setError(data.message || __('portal.error_fetch', 'No se pudieron cargar tus citas.'));
      }
    } catch (err) {
      console.error('Error fetching patient appointments:', err);
      setError(__('portal.error_network', 'Error de conexión al cargar las citas.'));
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchAppointments();
  }, []);

  const handleCancelConfirm = async (appointmentId: number) => {
    setSubmittingCancel(true);

    try {
      const appSettings = (window as any).AppTranslations;
      const headers: Record<string, string> = {
        'Content-Type': 'application/json',
      };
      if (appSettings?.nonce) {
        headers['X-WP-Nonce'] = appSettings.nonce;
      }

      const response = await fetch(`/wp-json/api/v1/patient/appointments/${appointmentId}/cancel`, {
        method: 'POST',
        headers,
      });

      const data = await response.json();

      if (response.ok && data.success) {
        setCancellingAppointment(null);
        setToastMessage(__('portal.cancel_success', 'Cita cancelada correctamente.'));
        setTimeout(() => setToastMessage(null), 4000);
        fetchAppointments();
      } else {
        alert(data.message || __('portal.cancel_error', 'No se pudo cancelar la cita.'));
      }
    } catch (err) {
      console.error('Error cancelling appointment:', err);
      alert(__('portal.cancel_error_network', 'Error de conexión al cancelar la cita.'));
    } finally {
      setSubmittingCancel(false);
    }
  };

  // Filter appointments according to tab and search query
  const now = Date.now();

  const isUpcoming = (app: Appointment) => {
    const time = new Date(app.appointment_date.replace(' ', 'T')).getTime();
    return time > now && app.status !== 'cancelled' && app.status !== 'completed';
  };

  const isPast = (app: Appointment) => {
    const time = new Date(app.appointment_date.replace(' ', 'T')).getTime();
    return (time <= now || app.status === 'completed') && app.status !== 'cancelled';
  };

  const upcomingCount = appointments.filter(isUpcoming).length;
  const pastCount = appointments.filter(isPast).length;
  const cancelledCount = appointments.filter(a => a.status === 'cancelled').length;

  const filteredAppointments = appointments.filter(app => {
    // Filter by tab
    if (activeTab === 'upcoming' && !isUpcoming(app)) return false;
    if (activeTab === 'past' && !isPast(app)) return false;
    if (activeTab === 'cancelled' && app.status !== 'cancelled') return false;

    // Filter by search query (doctor name or specialty)
    if (searchQuery.trim() !== '') {
      const q = searchQuery.toLowerCase();
      const nameMatch = app.doctor_name.toLowerCase().includes(q);
      const specMatch = app.doctor_specialty.toLowerCase().includes(q);
      return nameMatch || specMatch;
    }

    return true;
  });

  return (
    <div className="relative min-h-screen bg-slate-950 text-white font-sans overflow-hidden py-12 px-6">
      {/* Background glow effects */}
      <div className="absolute top-0 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full filter blur-3xl animate-pulse pointer-events-none" />
      <div className="absolute bottom-10 right-1/4 w-96 h-96 bg-teal-500/10 rounded-full filter blur-3xl animate-pulse pointer-events-none" style={{ animationDelay: '2s' }} />

      <div className="max-w-6xl mx-auto space-y-10 relative z-10">
        
        {/* Toast Alert Banner */}
        {toastMessage && (
          <div className="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-bold text-sm flex items-center gap-3 shadow-xl backdrop-blur-xl animate-fade-in">
            <svg className="w-5 h-5 shrink-0" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
            <span>{toastMessage}</span>
          </div>
        )}

        {/* Dashboard Welcome Header */}
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-xl shadow-2xl">
          <div>
            <span className="text-xs font-bold text-emerald-400 uppercase tracking-widest bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">
              {__('portal.tag', 'Portal del Paciente')}
            </span>
            <h1 className="text-3xl sm:text-4xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-emerald-400 mt-3">
              {__('portal.welcome_title', 'Mis Citas Médicas')}
            </h1>
            <p className="mt-2 text-slate-400 text-sm max-w-xl">
              {__('portal.welcome_desc', 'Consulta y gestiona tus próximas consultas, revisa tu historial clínico y mantén al día tu agenda de salud.')}
            </p>
          </div>

          <a
            href="/doctors/"
            className="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 font-extrabold text-sm transition-all shadow-lg shadow-emerald-500/20 active:scale-95 shrink-0"
          >
            <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>{__('portal.btn_new_booking', 'Reservar Nueva Cita')}</span>
          </a>
        </div>

        {/* Stats Summary Cards (Shadcn Widget Style) */}
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
          <div className="bg-white/5 border border-white/10 p-5 rounded-2xl backdrop-blur-xl flex items-center justify-between">
            <div>
              <p className="text-xs font-semibold text-slate-400 uppercase tracking-wider">{__('portal.kpi_upcoming', 'Próximas Citas')}</p>
              <h3 className="text-2xl font-black text-emerald-400 mt-1">{upcomingCount}</h3>
            </div>
            <div className="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
              </svg>
            </div>
          </div>

          <div className="bg-white/5 border border-white/10 p-5 rounded-2xl backdrop-blur-xl flex items-center justify-between">
            <div>
              <p className="text-xs font-semibold text-slate-400 uppercase tracking-wider">{__('portal.kpi_past', 'Historial')}</p>
              <h3 className="text-2xl font-black text-white mt-1">{pastCount}</h3>
            </div>
            <div className="w-10 h-10 rounded-xl bg-slate-800 border border-white/10 text-slate-300 flex items-center justify-center">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>

          <div className="bg-white/5 border border-white/10 p-5 rounded-2xl backdrop-blur-xl flex items-center justify-between">
            <div>
              <p className="text-xs font-semibold text-slate-400 uppercase tracking-wider">{__('portal.kpi_cancelled', 'Canceladas')}</p>
              <h3 className="text-2xl font-black text-red-400 mt-1">{cancelledCount}</h3>
            </div>
            <div className="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center justify-center">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>

          <div className="bg-white/5 border border-white/10 p-5 rounded-2xl backdrop-blur-xl flex items-center justify-between">
            <div>
              <p className="text-xs font-semibold text-slate-400 uppercase tracking-wider">{__('portal.kpi_total', 'Total Registradas')}</p>
              <h3 className="text-2xl font-black text-teal-300 mt-1">{appointments.length}</h3>
            </div>
            <div className="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
              </svg>
            </div>
          </div>
        </div>

        {/* Controls Toolbar: Tabs & Search Input */}
        <div className="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 border-b border-white/10 pb-6">
          {/* Shadcn-Style Segmented Tabs */}
          <div className="flex items-center gap-1 bg-slate-900/80 p-1.5 rounded-2xl border border-white/10 overflow-x-auto shrink-0">
            <button
              onClick={() => setActiveTab('upcoming')}
              className={`px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap ${
                activeTab === 'upcoming'
                  ? 'bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/20'
                  : 'text-slate-400 hover:text-white hover:bg-white/5'
              }`}
            >
              {__('portal.tab_upcoming', 'Próximas')} ({upcomingCount})
            </button>

            <button
              onClick={() => setActiveTab('past')}
              className={`px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap ${
                activeTab === 'past'
                  ? 'bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/20'
                  : 'text-slate-400 hover:text-white hover:bg-white/5'
              }`}
            >
              {__('portal.tab_past', 'Historial')} ({pastCount})
            </button>

            <button
              onClick={() => setActiveTab('cancelled')}
              className={`px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap ${
                activeTab === 'cancelled'
                  ? 'bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/20'
                  : 'text-slate-400 hover:text-white hover:bg-white/5'
              }`}
            >
              {__('portal.tab_cancelled', 'Canceladas')} ({cancelledCount})
            </button>

            <button
              onClick={() => setActiveTab('all')}
              className={`px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap ${
                activeTab === 'all'
                  ? 'bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/20'
                  : 'text-slate-400 hover:text-white hover:bg-white/5'
              }`}
            >
              {__('portal.tab_all', 'Todas')} ({appointments.length})
            </button>
          </div>

          {/* Search Bar Input */}
          <div className="relative w-full md:w-72">
            <input
              type="text"
              value={searchQuery}
              onChange={e => setSearchQuery(e.target.value)}
              placeholder={__('portal.search_placeholder', 'Buscar por médico o especialidad...')}
              className="w-full bg-slate-900/60 border border-white/10 hover:border-emerald-500/30 text-white placeholder-slate-500 text-xs px-4 py-2.5 pl-9 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
            />
            <svg className="w-4 h-4 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            {searchQuery && (
              <button
                onClick={() => setSearchQuery('')}
                className="absolute right-3 top-2.5 text-slate-500 hover:text-white text-xs font-bold"
              >
                ✕
              </button>
            )}
          </div>
        </div>

        {/* Loading Spinner State */}
        {loading && (
          <div className="flex flex-col items-center justify-center py-20 text-slate-400">
            <svg className="animate-spin h-10 w-10 text-emerald-500 mb-4" fill="none" viewBox="0 0 24 24">
              <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
              <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p className="text-xs font-semibold">{__('portal.loading', 'Cargando tu Portal de Paciente...')}</p>
          </div>
        )}

        {/* Error Alert State */}
        {error && !loading && (
          <div className="p-6 rounded-3xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm font-semibold flex items-center justify-between gap-4">
            <div className="flex items-center gap-3">
              <svg className="w-6 h-6 shrink-0" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <span>{error}</span>
            </div>
            <button
              onClick={fetchAppointments}
              className="px-4 py-2 rounded-xl bg-red-500/20 hover:bg-red-500 text-white font-bold text-xs transition-colors shrink-0"
            >
              Reintentar
            </button>
          </div>
        )}

        {/* Appointments Grid */}
        {!loading && !error && (
          <>
            {filteredAppointments.length > 0 ? (
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {filteredAppointments.map(app => (
                  <AppointmentCard
                    key={app.id}
                    appointment={app}
                    onCancelRequest={selected => setCancellingAppointment(selected)}
                  />
                ))}
              </div>
            ) : (
              /* Empty State Graphic */
              <div className="text-center py-20 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-xl space-y-4 max-w-2xl mx-auto">
                <div className="w-16 h-16 rounded-full bg-slate-900 border border-white/10 text-slate-500 mx-auto flex items-center justify-center">
                  <svg className="w-8 h-8 text-emerald-500/50" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                  </svg>
                </div>
                <h3 className="text-xl font-bold text-white">
                  {searchQuery 
                    ? __('portal.empty_search_title', 'No se encontraron citas') 
                    : __('portal.empty_title', 'No tienes citas registradas en esta sección')}
                </h3>
                <p className="text-slate-400 text-xs max-w-md mx-auto">
                  {searchQuery
                    ? __('portal.empty_search_desc', 'Prueba modificando tus términos de búsqueda o limpiando el filtro.')
                    : __('portal.empty_desc', 'Explora nuestro directorio médico enterprise y agenda tu cita con los mejores especialistas.')}
                </p>
                {!searchQuery && (
                  <div className="pt-2">
                    <a
                      href="/doctors/"
                      className="inline-flex items-center justify-center px-6 py-3 rounded-2xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 font-bold text-xs transition-all shadow-md shadow-emerald-500/20 active:scale-95"
                    >
                      {__('portal.btn_explore_doctors', 'Explorar Directorio Médico')}
                    </a>
                  </div>
                )}
              </div>
            )}
          </>
        )}

      </div>

      {/* Confirmation Modal */}
      <CancelModal
        appointment={cancellingAppointment}
        isOpen={cancellingAppointment !== null}
        onClose={() => setCancellingAppointment(null)}
        onConfirm={handleCancelConfirm}
        submitting={submittingCancel}
      />
    </div>
  );
}
