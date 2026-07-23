import { createElement, createRoot } from '@wordpress/element';
import { MedicalSearchDirectory } from './components/MedicalSearchDirectory';
import { DoctorBooking } from './components/DoctorBooking';
import { PatientDashboard } from './components/patient/PatientDashboard';

document.addEventListener('DOMContentLoaded', () => {
    const rootElement = document.getElementById('medical-search-root');
    if (rootElement) {
        const root = createRoot(rootElement);
        root.render(createElement(MedicalSearchDirectory));
    }

    const bookingElement = document.getElementById('doctor-booking-root');
    if (bookingElement) {
        const doctorId = parseInt(bookingElement.getAttribute('data-doctor-id') || '0', 10);
        const initialDate = bookingElement.getAttribute('data-initial-date') || '';
        const initialScheduleRaw = bookingElement.getAttribute('data-initial-schedule') || '{}';
        
        let initialSchedule = null;
        try {
            initialSchedule = JSON.parse(initialScheduleRaw);
        } catch (e) {
            console.error('Error parsing initial schedule', e);
        }

        const root = createRoot(bookingElement);
        root.render(createElement(DoctorBooking, { doctorId, initialDate, initialSchedule }));
    }

    const patientDashboardElement = document.getElementById('patient-dashboard-root');
    if (patientDashboardElement) {
        const root = createRoot(patientDashboardElement);
        root.render(createElement(PatientDashboard));
    }
});