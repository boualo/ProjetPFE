
document.addEventListener('DOMContentLoaded', function() {
    const schoolLevelSelect = document.getElementById('filiereSelect');
    const filiereSelect = document.getElementById('groupSelect');
    const content = document.getElementById('content');
    schoolLevelSelect.addEventListener('change', function() {
      const schoolLevelId = schoolLevelSelect.value;
      fetchGroupes(schoolLevelId);
    });
    const button = document.createElement('button');
    button.type = 'submit';
    button.className = 'btn btn-primary mt-3';
    button.textContent = 'Valider';

    // Get the empty div by its ID and append the button to it
    const buttonContainer = document.getElementById('content');
    

    function fetchGroupes(schoolLevelId) {
      fetch('/get_groupes', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `school_level_id=${schoolLevelId}`,
      })

      .then(response => response.json())
      .then(data => {
        filiereSelect.innerHTML = '';
        data.forEach(filiere => {
          const option = document.createElement('option');
          option.value = filiere.id;
          option.textContent = filiere.name;
          filiereSelect.appendChild(option);
        });
        buttonContainer.appendChild(button);
      });
    }
  });
  