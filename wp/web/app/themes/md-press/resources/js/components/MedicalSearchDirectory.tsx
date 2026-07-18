import { useState } from 'react';

export function MedicalSearchDirectory() {
    const [query, setQuery] = useState('');
    const [specialty, setSpecialty] = useState('');

    return (
        <div className="space-y-4">
            <h2 className="text-xl font-bold text-white border-b border-white/10 pb-2 flex items-center gap-2">
                <svg className="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Directorio de Especialistas
            </h2>
            <div className="space-y-3">
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
                        onChange={(e) => setSpecialty(e.target.value)}
                        className="w-full bg-slate-900/80 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-sm cursor-pointer"
                    >
                        <option value="">Todas las especialidades</option>
                        <option value="cardiology">Cardiología</option>
                        <option value="pediatrics">Pediatría</option>
                        <option value="dermatology">Dermatología</option>
                        <option value="neurology">Neurología</option>
                    </select>
                </div>
                <button 
                    onClick={() => alert(`Buscando: ${query || 'Todo'} en ${specialty || 'todas las especialidades'}`)}
                    className="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-semibold py-2.5 px-4 rounded-lg shadow-lg hover:shadow-emerald-500/20 active:scale-[0.98] transition-all duration-200 mt-2 text-sm"
                >
                    Buscar Especialistas
                </button>
            </div>
        </div>
    );
}