$j = jQuery.noConflict();

$j(function () {
    // Initialisation du datepicker
    if ($j("#datepicker").length) {
        $j.datepicker.setDefaults($j.datepicker.regional['fr']);
        $j("#datepicker").datepicker({
            dateFormat: "yy-mm-dd",
            firstDay: 1,
            showOtherMonths: true,
            selectOtherMonths: true,
            minDate: 0,
            maxDate: "+1M",
            beforeShowDay: function (date) {
                var day = date.getDay();
                return [(day != 0)]; // Désactiver les dimanches
            },
            onSelect: function (dateText) {
                var date = new Date(dateText);

                $j("#selectedDate").val(dateText); // Mettre à jour la date sélectionnée

                // Requête AJAX pour récupérer les créneaux réservés de cette date
                $j.ajax({
                    url: '/reservations/check-slots',
                    type: 'GET',
                    data: {date: dateText}, // Envoi de la date au serveur
                    success: function (response) {
                        var reservedSlots = response.reservedSlots.map(function(slot) {
                            return slot.replace(/^0/, ''); // Supprime le zéro initial si présent
                        });

                        // Réinitialiser tous les créneaux avant de les mettre à jour
                        $j('.slot-checkbox').prop('checked', false); // Désélectionner toutes les cases à cocher
                        $j('.slot-btn').removeClass('btn-danger').addClass('btn-outline-secondary');
                        $j('.slot-checkbox').removeClass('disabled').prop('disabled', false); // Réactiver tous les créneaux

                        // Définition des créneaux disponibles en fonction du jour de la semaine
                        var dayOfWeek = date.getDay(); // 0 - dimanche, 1 - lundi, ..., 6 - samedi
                        var availableSlots = [];

                        if (dayOfWeek >= 1 && dayOfWeek <= 5) { // Lundi - Vendredi
                            availableSlots = [
                                '9:00', '9:30', '10:00', '10:30', '11:00', '11:30', '12:00',
                                '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00',
                                '16:30', '17:00', '17:30', '18:00'
                            ];
                        } else if (dayOfWeek == 6) { // Samedi (jusqu'à 14h uniquement)
                            availableSlots = [
                                '9:00', '9:30', '10:00', '10:30', '11:00', '11:30', '12:00',
                                '13:00', '13:30', '14:00'
                            ];
                        }

                        // Désactiver les créneaux réservés et ceux qui ne sont pas dans availableSlots
                        $j('.slot-checkbox').each(function () {
                            var horaire = $j(this).val();
                            if (reservedSlots.includes(horaire) || !availableSlots.includes(horaire)) {
                                // Désactiver les créneaux réservés ou hors horaires disponibles
                                $j(this).siblings('label').addClass('disabled').prop('disabled', true);
                                $j(this).addClass('disabled').prop('disabled', true);
                            } else {
                                // Réactiver les créneaux disponibles
                                $j(this).siblings('label').removeClass('disabled').prop('disabled', false);
                                $j(this).removeClass('disabled').prop('disabled', false);
                            }
                        });

                        checkFormValidity();
                    },
                    error: function () {
                        alert('Erreur lors de la récupération des créneaux réservés');
                    }
                });
            }
        });
    }

    // Permet de sélectionner un seul créneau à la fois
    $j('.slot-checkbox').on('change', function () {
        $j('.slot-checkbox').not(this).prop('checked', false);
        $j('.slot-btn').removeClass('btn-danger').addClass('btn-outline-secondary');
        if (this.checked) {
            $j(this).next('label').removeClass('btn-outline-secondary').addClass('btn-danger');
        }
    });

    // Mettre à jour les prestations selon la catégorie choisie
    $j('#categorySelect').on('change', function () {
        var category = $j(this).val();
        if (category) { // Requête AJAX pour récupérer les prestations en fonction du sexe choisi
            $j.ajax({
                url: '/services/filtered/' + category, method: 'GET', success: function (data) {
                    $j('#prestationSelect').empty();
                    $j('#prestationSelect').append('<option disabled selected>Choisissez un service</option>');
                    data.forEach(function (service) {
                        $j('#prestationSelect').append('<option value="' + service.id + '">' + service.name + '</option>');
                    });
                }
            });
        }
    });
});


// Partie pour la validation du formulaire
$j(document).ready(function () {
    // Mise à jour dynamique des prestations en fonction du genre
    $j('#categorySelect').on('change', function () {
        var selectedCategory = $j(this).val();
        $j('#prestationSelect option').each(function () {
            if ($j(this).data('category') === selectedCategory || $j(this).val() === '') {
                $j(this).show();
            } else {
                $j(this).hide();
            }
        });
        $j('#prestationSelect').val('');
        checkFormValidity(); // Vérifie la validité du formulaire
    });

    // Validation de la prestation sélectionnée
    $j('#prestationSelect').on('change', function () {
        var selectedPrestation = $j(this).val();
        $j('#selectedPrestation').val(selectedPrestation);
        checkFormValidity(); // Vérifie la validité du formulaire
    });

    // Sélection des créneaux horaires
    $j('.slot-checkbox').on('change', function () {
        var selectedHoraire = $j(this).val();
        $j('#selectedHoraire').val(selectedHoraire);
        checkFormValidity(); // Vérifie la validité du formulaire
    });
});

// Fonction pour vérifier si tout est bien sélectionné
function checkFormValidity() {
    var prestationSelected = $j('#prestationSelect').val();
    var slotSelected = $j('.slot-checkbox:checked').length > 0;

    if (prestationSelected && slotSelected) {
        $j('#confirmButton').prop('disabled', false); // Activer le bouton de confirmation
    } else {
        $j('#confirmButton').prop('disabled', true); // Désactiver le bouton de confirmation
    }
}
