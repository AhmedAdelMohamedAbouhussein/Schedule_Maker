function validateEditForm() 
{
    const studentID = document.getElementById('studentID').value.trim();
    const responseDiv = document.getElementById('response');

    // Validate Student ID: 9 digits
    if (!/^\d{9}$/.test(studentID)) 
    {
        responseDiv.innerHTML = "Student ID must be exactly 9 digits.";
        return false;
    }

    return true;
}
