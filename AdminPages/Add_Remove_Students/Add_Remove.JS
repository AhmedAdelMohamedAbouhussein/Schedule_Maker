function openModal(type) 
{
    document.getElementById(type + "-modal").classList.add("open");
    document.body.style.overflow = "hidden";
}
function closeModal(type) {
    document.getElementById(type + "-modal").classList.remove("open");
    document.body.style.overflow = "";
    if (type === "add") document.getElementById("add-response").innerHTML = "";
    if (type === "remove") document.getElementById("remove-response").innerHTML = "";
}
document.querySelectorAll(".modal").forEach(function (modal) {
    modal.addEventListener("click", function (e) {
        if (e.target === modal || e.target.classList.contains("modal-bg-blur")) {
            closeModal(modal.id.replace("-modal", ""));
        }
    });
});

function validateID() 
{
    const studentID = document.getElementById('remove-studentID').value.trim();
    const responseDiv = document.getElementById('remove-response');

    if (!/^\d{9}$/.test(studentID)) 
    {
        responseDiv.innerHTML = "Student ID must be exactly 9 digits.";
        return false;
    }

    return true;
}


function validateAddForm() 
{
    const studentID = document.getElementById('add-studentID').value.trim();
    const pin = document.getElementById('add-pin').value.trim();
    const departmentID = parseInt(document.getElementById('add-departmentID').value.trim());
    const term = parseInt(document.getElementById('add-term').value.trim());
    const name = document.getElementById('add-name').value.trim();  
    const responseDiv = document.getElementById('add-response');

    console.log("departmentID:", departmentID, "isNaN:", isNaN(departmentID));
    console.log("term:", term, typeof term);


    // Validate Student ID: 9 digits
    if (!/^\d{9}$/.test(studentID)) 
    {
        responseDiv.innerHTML = "Student ID must be exactly 9 digits.";
        return false;
    }

    // Validate PIN: 6 digits
    if (!/^\d{6}$/.test(pin))  
    {
        responseDiv.innerHTML = "PIN must be exactly 6 digits.";
        return false;
    }

    // Validate Department ID
    if (![1,2,3,4].includes(departmentID)) 
    {
        responseDiv.innerHTML = "Department ID must be between 1 and 4.";
        return false;
    }

    // Validate Term based on Department
    let maxTerm = 0;

    console.log("departmentID:", departmentID, typeof departmentID);

    if (departmentID == 1 || departmentID == 3) 
    {
        console.log("Setting maxTerm = 8");
        maxTerm = 8;
    } 
    else if (departmentID == 2) 
    {
        console.log("Setting maxTerm = 10");
        maxTerm = 10;
    } 
    else if (departmentID == 4) 
        {
        console.log("Setting maxTerm = 6");
        maxTerm = 6;
    } 
    else 
    {
        console.log("Invalid departmentID");
    }
    console.log("maxTerm after ifs:", maxTerm); 


    if (term < 1) 
    {
        responseDiv.innerHTML = 'For Department '+ departmentID +' Term must be bigger tham';
        return false;
    }
    console.log("maxTerm after ifs:", maxTerm); 
    if (term > maxTerm) 
    {
        responseDiv.innerHTML = 'For Department '+ departmentID +' Term must be between 1 and '+ maxTerm;
        return false;
    }
    console.log("maxTerm after ifs:", maxTerm); 

    //  Validate Name: longer than 8 characters
    if (name.length <= 8) 
    {
        responseDiv.innerHTML = "Name must be longer than 8 characters.";
        return false;
    }
    return true;
}
