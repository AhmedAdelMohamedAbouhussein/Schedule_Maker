function validateID ()
{
    const studentID = document.getElementById('studentID').value.trim();

    // Validate Student ID
    if (!/^\d{9}$/.test(studentID)) 
    {
        document.getElementById('response').innerHTML = "Student ID must be exactly 9 digits.";
        return false;
    }
    else
    {
        return true;
    }
}