function fakeLogin() 
{
    let user = document.getElementById('ID').value.trim();
            let pass = document.getElementById('PIN').value.trim();

            const IDValid = /^[0-9]{9}$/.test(user);
            const PINValid = /^[0-9]{6}$/.test(pass);

            if (!IDValid) {
                document.getElementById('error-message').innerText = "Invalid ID. It should be exactly 9 digits.";
                document.getElementById('error-message').style.display = 'block';
                return false;
            } else if (!PINValid) {
                document.getElementById('error-message').innerText = "Invalid PIN. It should be exactly 6 digits.";
                document.getElementById('error-message').style.display = 'block';
                return false;
            } else {
                document.getElementById('error-message').style.display = 'none';
                return true;
            }
        }
    
        function togglePIN() {
            const pinInput = document.getElementById('PIN');
            const icon = document.getElementById('toggle-icon');

            if (pinInput.type === 'password') {
                pinInput.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                pinInput.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        }