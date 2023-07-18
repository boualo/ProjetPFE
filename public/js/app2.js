
document.addEventListener('DOMContentLoaded', function() {
    const schoolLevelSelect = document.getElementById('filiereSelect');
    const filiereSelect = document.getElementById('groupSelect');
    schoolLevelSelect.addEventListener('change', function() {
      const schoolLevelId = schoolLevelSelect.value;
      fetchGroupes(schoolLevelId);
    });

    

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
      });
    }
  });
  