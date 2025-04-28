import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your Back_Base.html.twig.
 */
import './styles/app.css';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
document.addEventListener('DOMContentLoaded', () => {
    const calendarEl = document.getElementById('calendar');

    if (calendarEl) {
        const calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth', // Vue par défaut
            events: '/api/events', // Endpoint pour récupérer les événements
            locale: 'fr', // Langue du calendrier
            editable: true, // Permet de déplacer les événements
            dateClick: function(info) {
                alert('Date cliquée : ' + info.dateStr);
            },
            eventClick: function(info) {
                window.location.href = info.event.url; // Redirige vers la page de l'événement
            }
        });

        calendar.render();
    }
});