// public/js/app.js
document.addEventListener('DOMContentLoaded', function() {
    const schoolLevelSelect = document.getElementById('schoolLevelSelect');
    const filiereSelect = document.getElementById('filiereSelect');
  
    schoolLevelSelect.addEventListener('change', function() {
      const schoolLevelId = schoolLevelSelect.value;
      fetchFilieres(schoolLevelId);
    });
  
    function fetchFilieres(schoolLevelId) {
      fetch('/get_filieres', {
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
      });
    }
  });
  