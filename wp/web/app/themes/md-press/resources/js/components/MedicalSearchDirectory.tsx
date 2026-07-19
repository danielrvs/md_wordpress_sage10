import React from 'react';
import { useState, useEffect } from '@wordpress/element';

interface Doctor {
  id: number;
  name: string;
  specialty: string;
  location: string;
  availability: string;
  rating: number;
  permalink: string;
  thumbnail: string;
}

export function MedicalSearchDirectory() {
  const isHomepage = window.location.pathname === '/' || window.location.pathname === '/wp/' || window.location.pathname === '/index.php';

  const [query, setQuery] = useState('');
  const [specialty, setSpecialty] = useState('');
  const [doctors, setDoctors] = useState<Doctor[]>([]);
  const [loading, setLoading] = useState(!isHomepage);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  const fetchDoctors = async (currentPage = 1, searchQuery = query, specialtyFilter = specialty) => {
    setLoading(true);
    try {
      const url = new URL('/wp-json/api/v1/doctors', window.location.origin);
      url.searchParams.append('page', currentPage.toString());
      url.searchParams.append('per_page', '6');
      if (searchQuery) url.searchParams.append('search', searchQuery);
      if (specialtyFilter) url.searchParams.append('specialty', specialtyFilter);

      const response = await fetch(url.toString());
      if (response.ok) {
        const data = await response.json();
        setDoctors(data.doctors || []);
        setTotalPages(data.pages || 1);
        setPage(currentPage);
      }
    } catch (error) {
      console.error('Error fetching doctors:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (!isHomepage) {
      const params = new URLSearchParams(window.location.search);
      const searchParam = params.get('search') || '';
      const specialtyParam = params.get('specialty') || '';
      
      setQuery(searchParam);
      setSpecialty(specialtyParam);
      
      fetchDoctors(1, searchParam, specialtyParam);
    }
  }, []);

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (isHomepage) {
      const targetUrl = `/doctors?search=${encodeURIComponent(query)}&specialty=${encodeURIComponent(specialty)}`;
      window.location.href = targetUrl;
    } else {
      fetchDoctors(1);
    }
  };

  const handleSpecialtyChange = (val: string) => {
    setSpecialty(val);
    if (!isHomepage) {
      fetchDoctors(1, query, val);
    }
  };

  if (isHomepage) {
    return (
      <div className="space-y-4">
        <h2 className="text-xl font-bold text-white border-b border-white/10 pb-2 flex items-center gap-2">
          <svg className="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          Directorio de Especialistas
        </h2>
        <form onSubmit={handleSearchSubmit} className="space-y-3">
          <div>
            <label className="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nombre o Palabra Clave</label>
            <input 
              type="text" 
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              placeholder="Ej. Dr. Alejandro Ruiz, Pediatría..." 
              className="w-full bg-slate-900/80 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-sm"
            />
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Especialidad</label>
            <select 
              value={specialty}
              onChange={(e) => handleSpecialtyChange(e.target.value)}
              className="w-full bg-slate-900/80 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-sm cursor-pointer"
            >
              <option value="">Todas las especialidades</option>
              <option value="Cardiología">Cardiología</option>
              <option value="Pediatría">Pediatría</option>
              <option value="Dermatología">Dermatología</option>
              <option value="Neurología">Neurología</option>
              <option value="Medicina General">Medicina General</option>
            </select>
          </div>
          <button 
            type="submit"
            className="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-semibold py-2.5 px-4 rounded-lg shadow-lg hover:shadow-emerald-500/20 active:scale-[0.98] transition-all duration-200 mt-2 text-sm cursor-pointer"
          >
            Buscar Especialistas
          </button>
        </form>
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {/* Search Bar Form */}
      <form onSubmit={handleSearchSubmit} className="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl shadow-2xl relative group hover:border-emerald-500/20 transition-all duration-300">
        <div className="absolute -inset-0.5 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl blur opacity-5 group-hover:opacity-10 transition duration-300"></div>
        <div className="relative grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
          
          <div className="md:col-span-6">
            <label className="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Nombre o palabra clave</label>
            <div className="relative">
              <input 
                type="text" 
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                placeholder="Ej. Dr. Alejandro Ruiz, Pediatra..." 
                className="w-full bg-slate-900/80 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all"
              />
              <div className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500">
                <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>
          </div>

          <div className="md:col-span-4">
            <label className="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Especialidad</label>
            <select 
              value={specialty}
              onChange={(e) => handleSpecialtyChange(e.target.value)}
              className="w-full bg-slate-900/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all cursor-pointer"
            >
              <option value="">Todas las especialidades</option>
              <option value="Cardiología">Cardiología</option>
              <option value="Pediatría">Pediatría</option>
              <option value="Dermatología">Dermatología</option>
              <option value="Neurología">Neurología</option>
              <option value="Medicina General">Medicina General</option>
            </select>
          </div>

          <div className="md:col-span-2">
            <button 
              type="submit"
              className="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-bold py-2.5 px-4 rounded-xl shadow-lg hover:shadow-emerald-500/20 active:scale-[0.98] transition-all duration-200 text-sm cursor-pointer"
            >
              Buscar
            </button>
          </div>

        </div>
      </form>

      {/* Grid of Results */}
      {loading ? (
        <div className="flex flex-col items-center justify-center py-20 text-slate-400">
          <svg className="animate-spin h-10 w-10 text-emerald-500 mb-4" fill="none" viewBox="0 0 24 24">
            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p className="text-sm font-semibold tracking-wide">Cargando especialistas...</p>
        </div>
      ) : doctors.length === 0 ? (
        <div className="p-12 text-center rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl">
          <svg className="w-12 h-12 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
          </svg>
          <p className="text-slate-400 font-medium">No se encontraron especialistas que coincidan con los filtros.</p>
        </div>
      ) : (
        <div className="space-y-8">
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {doctors.map((doctor) => {
              const fullStars = Math.floor(doctor.rating);
              const hasHalf = (doctor.rating - fullStars) >= 0.5;
              
              let availabilityClass = "bg-slate-500/10 text-slate-400 border-slate-500/20";
              if (doctor.availability === 'Inmediata') {
                availabilityClass = "bg-emerald-500/10 text-emerald-400 border-emerald-500/20";
              } else if (doctor.availability === 'Esta semana') {
                availabilityClass = "bg-teal-500/10 text-teal-400 border-teal-500/20";
              }

              return (
                <div key={doctor.id} className="flex flex-col justify-between rounded-2xl bg-white/5 border border-white/10 overflow-hidden hover:border-emerald-500/30 transition-all duration-300 group hover:-translate-y-1 shadow-lg shadow-black/10">
                  <div className="p-6 space-y-4">
                    
                    <div className="flex gap-4 items-start">
                      <div className="w-16 h-16 rounded-full overflow-hidden bg-slate-900 border border-white/10 flex items-center justify-center shrink-0">
                        {doctor.thumbnail ? (
                          <img src={doctor.thumbnail} alt={doctor.name} className="w-full h-full object-cover" />
                        ) : (
                          <svg className="w-8 h-8 text-emerald-500/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                          </svg>
                        )}
                      </div>
                      <div className="space-y-1.5 min-w-0">
                        <span className="inline-block text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-0.5 rounded-full uppercase tracking-wider truncate max-w-full">
                          {doctor.specialty}
                        </span>
                        <h3 className="text-base font-extrabold text-white leading-tight group-hover:text-emerald-300 transition-colors truncate">
                          <a href={doctor.permalink}>{doctor.name}</a>
                        </h3>
                      </div>
                    </div>

                    <div className="border-t border-white/5 pt-4 space-y-2.5 text-xs text-slate-400">
                      <div className="flex items-center gap-2">
                        <svg className="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                          <path strokeLinecap="round" strokeLinejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span className="truncate">{doctor.location}</span>
                      </div>
                      
                      <div className="flex items-center gap-2">
                        <svg className="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span className={`inline-block text-[9px] font-bold border px-2 py-0.5 rounded ${availabilityClass}`}>
                          {doctor.availability}
                        </span>
                      </div>

                      <div className="flex items-center gap-1.5 pt-1">
                        <div className="flex items-center text-amber-400">
                          {Array.from({ length: 5 }).map((_, index) => {
                            const i = index + 1;
                            if (i <= fullStars) {
                              return <svg key={i} className="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>;
                            } else if (i === fullStars + 1 && hasHalf) {
                              return <svg key={i} className="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><defs><linearGradient id={`half-${doctor.id}`}><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#475569" stop-opacity="1"/></linearGradient></defs><path fill={`url(#half-${doctor.id})`} d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>;
                            } else {
                              return <svg key={i} className="w-3.5 h-3.5 text-slate-600 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>;
                            }
                          })}
                        </div>
                        <span className="text-[10px] text-slate-500 font-bold">({doctor.rating.toFixed(1)})</span>
                      </div>
                    </div>

                  </div>

                  <div className="p-4 bg-white/5 border-t border-white/5 flex">
                    <a href={doctor.permalink} className="w-full text-center bg-slate-900/60 hover:bg-emerald-500 hover:text-slate-950 text-slate-300 font-bold py-2 rounded-xl text-xs transition-all duration-200 cursor-pointer">
                      Ver Perfil Completo
                    </a>
                  </div>

                </div>
              );
            })}
          </div>

          {/* Pagination */}
          {totalPages > 1 && (
            <div className="flex justify-center gap-4 pt-4">
              <button 
                onClick={() => fetchDoctors(page - 1)}
                disabled={page === 1}
                className="px-4 py-2 text-xs font-bold bg-white/5 border border-white/10 hover:border-emerald-500/30 text-white rounded-xl disabled:opacity-30 disabled:pointer-events-none transition-all cursor-pointer"
              >
                &larr; Anterior
              </button>
              <span className="flex items-center text-xs text-slate-400 font-bold">Página {page} de {totalPages}</span>
              <button 
                onClick={() => fetchDoctors(page + 1)}
                disabled={page === totalPages}
                className="px-4 py-2 text-xs font-bold bg-white/5 border border-white/10 hover:border-emerald-500/30 text-white rounded-xl disabled:opacity-30 disabled:pointer-events-none transition-all cursor-pointer"
              >
                Siguiente &rarr;
              </button>
            </div>
          )}
        </div>
      )}
    </div>
  );
}