
document.addEventListener('DOMContentLoaded', function() {
    const schoolLevelSelect = document.getElementById('groupSelect');
    const filiereSelect = document.getElementById('eleveSelect');
    
    schoolLevelSelect.addEventListener('change', function() {
      const schoolLevelId = schoolLevelSelect.value;
      fetchEleves(schoolLevelId);
    });

    

    function fetchEleves(schoolLevelId) {
      fetch('/get_eleves', {
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
          option.value = filiere.codeMassar;
          option.textContent = filiere.nom+" "+filiere.prenom+" CNE: "+filiere.codeMassar;
          filiereSelect.appendChild(option);
        });
      });
    }
  });
  
  