//Register
function validateForm(){
    const usernmame=document.getElementById('username').value.trim();
    const email=document.getElementById('email').value.trim();
    const password=document.getElementById('password').value.trim();
    const confirmpassword=document.getElementById('confirmpassword').value.trim();
    const role=document.getElementById('role').value.trim();
    const error=document.getElementById('error');

        let errorMessage = '';

    if (username === '') 
        {
        errorMessage = 'Full Name is required.';
    } 
    else if (email === '') 
        {
        errorMessage = 'Email Address is required.';
    } 
    else if (!validateEmail(email))
         {
        errorMessage = 'Please enter a valid email address.';
    } 
    else if (password === '') 
        {
        errorMessage = 'Password is required.';
    } 
    else if (password.length < 6) 
        {
        errorMessage = 'Password must be at least 6 characters.';
    } 
    else if (confirmPassword === '') 
        {
        errorMessage = 'Please confirm your password.';
    } 
    else if (password !== confirmPassword)
         {
        errorMessage = 'Passwords do not match.';
    } 
    else if (role === '') 
        {
        errorMessage = 'Please select a role.';
    }

    if (errorMessage !== '') 
        {
        errorBox.innerText = errorMessage;
        errorBox.style.display = 'block';
        return false; 
    } 
    else
     {
        errorBox.style.display = 'none';
        return true; 
    }
}
function validateEmail(email){
    const regex=/^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

//login
function validateLoginForm(){
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value.trim();
    const errorBox = document.getElementById('loginErrorBox');

    let errorMessage = '';

    if (email === '') {
        error= 'Email Address is required.';
    }
    else if (!validEmail(email)) {
        error= 'Please enter a valid email address.';
    }
    else if (password === '') {
        error= 'Password is required.';

    }
    if (error !== '') {
        errorBox.innerText = error;
        errorBox.style.display = 'block';
        return false;
    }
    else {
        errorBox.style.display = 'none';
        return true;

    }

}
function validEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}
//AJAX Email Check
document.getElementById("email").addEventListener("blur", function () {
    const email = this.value.trim();
    const statusBox = document.getElementById("emailStatus");

    if (email === "") {
        statusBox.innerText = "";
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "check_email.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText === "exists") {
                statusBox.style.color = "red";
                statusBox.innerText = "Email already exists";
            } else if (xhr.responseText === "available") {
                statusBox.style.color = "green";
                statusBox.innerText = "Email available";
            }
        }
    };

    xhr.send("email=" + encodeURIComponent(email));
});


