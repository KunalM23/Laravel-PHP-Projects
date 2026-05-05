const fs = require('fs');
const path = require('path');

const viewsDir = path.join(__dirname, 'resources/views');
const filesToFix = [
  'users/roles.blade.php',
  'users/list.blade.php',
  'users/add.blade.php',
  'tasks/list.blade.php',
  'tasks/add.blade.php',
  'leads/list.blade.php',
  'leads/add.blade.php',
  'interactions/list.blade.php',
  'interactions/add.blade.php'
];

filesToFix.forEach(file => {
  const filePath = path.join(viewsDir, file);
  if (!fs.existsSync(filePath)) {
    console.log('Skipping missing file:', file);
    return;
  }
  
  let content = fs.readFileSync(filePath, 'utf8');

  // Replace top part
  content = content.replace(/<div class="page-container">\s*@include\('layouts\.navbar'\)\s*(?:<!-- HEADER DESKTOP-->)?\s*/g, '');

  // Replace bottom part - remove the two closing </div>s right before @endsection
  content = content.replace(/<\/div>\s*<\/div>\s*@endsection/g, '@endsection');

  fs.writeFileSync(filePath, content);
  console.log('Fixed', file);
});
