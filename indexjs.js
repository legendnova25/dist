const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');

signUpButton.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});

signInButton.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
});

// Handle form submission for signup (Basic example)
const signupForm = document.querySelector('.sign-up-container form');

document.getElementById('search-button').addEventListener('click', function () {
    const query = document.getElementById('search-input').value;
    if (query) {
        window.location.href = `search.php?q=${query}`;
    }
});


