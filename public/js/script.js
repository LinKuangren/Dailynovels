// Script exécuté une fois que le document est prêt
document.addEventListener('DOMContentLoaded', function() {
    ProfileMenu();
    DeleteSecurity();
    DeleteSecurity2();
    readMoreLinks();
    star();
});


// Fonction menu profil
function ProfileMenu() {
    const profilePicture = document.getElementById('profile-picture');
    const profileMenu = document.getElementById('profile-menu');

    profilePicture.addEventListener('click', function(event) {
        event.stopPropagation(); // Empêcher la propagation du clic à l'extérieur de l'image
        if (profileMenu.style.display === 'block') {
            profileMenu.style.display = 'none'; // Cacher le menu s'il est déjà ouvert
        } else {
            profileMenu.style.display = 'block'; // Afficher le menu s'il est caché
        }
    });

    // Fermer le menu si l'utilisateur clique en dehors
    document.addEventListener('click', function(event) {
        if (event.target !== profilePicture && event.target !== profileMenu) {
            profileMenu.style.display = 'none'; // Cacher le menu si l'utilisateur clique en dehors
        }
    });
}

function DeleteSecurity() {
    const deleteLinks = document.querySelectorAll('.delete');
    const modal = document.getElementById('confirmation-modal');
    const messageElement = document.getElementById('confirmation-message');
    const confirmButton = document.getElementById('confirm-button');
    const cancelButton = document.getElementById('cancel-button');
    
    deleteLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const confirmationMessage = this.getAttribute('data-confirm');
            
            messageElement.textContent = confirmationMessage;
            modal.style.display = 'block';
            confirmButton.style.display = 'block';
            cancelButton.style.display = 'block';

            confirmButton.addEventListener('click', function () {
                modal.style.display = 'none';
                window.location.href = link.getAttribute('href');
            });

            cancelButton.addEventListener('click', function () {
                modal.style.display = 'none';
            });
        });
    });
}

function DeleteSecurity2() {
    const deleteLinks = document.querySelectorAll('.chap-delete');
    const modal = document.getElementById('confirmation-modal');
    const messageElement = document.getElementById('confirmation-message');
    const confirmButton = document.getElementById('confirm-button');
    const cancelButton = document.getElementById('cancel-button');
    
    deleteLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const confirmationMessage = this.getAttribute('data-confirm');
            
            messageElement.textContent = confirmationMessage;
            modal.style.display = 'block';
            confirmButton.style.display = 'block';
            cancelButton.style.display = 'block';

            confirmButton.addEventListener('click', function () {
                modal.style.display = 'none';
                window.location.href = link.getAttribute('href');
            });

            cancelButton.addEventListener('click', function () {
                modal.style.display = 'none';
            });
        });
    });
}

function readMoreLinks() {
    var readMoreLinks = document.getElementsByClassName('read-more-link');
    Array.from(readMoreLinks).forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            var descriptionId = this.getAttribute('data-description-id');
            var partialDescription = document.getElementById('partial-description-' + descriptionId);
            var fullDescription = document.getElementById('description-' + descriptionId);

            var isPartialHidden = partialDescription.style.display === 'none';

            partialDescription.style.display = isPartialHidden ? 'block' : 'none';
            fullDescription.style.display = isPartialHidden ? 'none' : 'block';
            this.innerText = isPartialHidden ? 'Suite >>' : 'Moins <<';
        });
    });
}

function star() {
    var stars = document.querySelectorAll('.star-label');

    stars.forEach(function (star) {
        star.addEventListener('click', function () {
            var starValue = this.getAttribute('data-star');
            updateStars(starValue);
        });
    });

    function updateStars(selectedStar) {
        stars.forEach(function (star) {
            var starNumber = star.getAttribute('data-star');
            var starIcon = star.querySelector('i');

            if (starNumber <= selectedStar) {
                starIcon.classList.remove('fa-regular');
                starIcon.classList.add('fa-solid');
            } else {
                starIcon.classList.remove('fa-solid');
                starIcon.classList.add('fa-regular');
            }
        });
    }
}