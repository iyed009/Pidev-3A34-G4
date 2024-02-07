const container1 = document.getElementById('container1');

const registerBtn = document.getElementById('register1');

const loginBtn = document.getElementById('login1');

registerBtn.addEventListener('click', () => {
    container1.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container1.classList.remove("active");
});

