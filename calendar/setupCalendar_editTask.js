function setupCalendars() {
        Calendar.setup({dateField: 'popupDateField',triggerElement: 'popupDateField'})
        }
Event.observe(window, 'load', function() { setupCalendars() })
