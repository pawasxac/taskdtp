import babel from '@babel/core';
import fs from 'fs';
import path from 'path';

function cleanJSContent(code, filename) {
  try {
    const ast = babel.parseSync(code, {
      filename: filename,
      parserOpts: {
        plugins: ['jsx']
      }
    });

    const comments = ast.comments.filter(c => c.type === 'CommentLine');
    comments.sort((a, b) => b.start - a.start);
    let cleaned = code;
    for (const c of comments) {
      cleaned = cleaned.slice(0, c.start) + cleaned.slice(c.end);
    }

    // Clean up trailing spaces from each line
    const lines = cleaned.split('\n');
    const cleanedLines = lines.map(line => line.trimEnd());
    return cleanedLines.join('\n');
  } catch (err) {
    // If Babel parsing fails (e.g. syntax errors or blade templates in script)
    // fallback to a safe regex that ignores URLs
    return code.replace(/(?<!http:|https:|ftp:|file:)\/\/.*$/gm, '').split('\n').map(l => l.trimEnd()).join('\n');
  }
}

function cleanBladeScriptTags(content, filename) {
  const scriptRegex = /(<script\b[^>]*>)([\s\S]*?)(<\/script>)/gi;
  return content.replace(scriptRegex, (match, openTag, scriptBody, closeTag) => {
    const cleanedBody = cleanJSContent(scriptBody, filename);
    return openTag + cleanedBody + closeTag;
  });
}

function processDirectory(dir) {
  const files = fs.readdirSync(dir);
  for (const file of files) {
    const fullPath = path.join(dir, file);
    const stat = fs.statSync(fullPath);
    if (stat.isDirectory()) {
      processDirectory(fullPath);
    } else {
      const ext = path.extname(file);
      if (ext === '.js' || ext === '.jsx') {
        const content = fs.readFileSync(fullPath, 'utf8');
        const cleaned = cleanJSContent(content, file);
        if (cleaned !== content) {
          fs.writeFileSync(fullPath, cleaned, 'utf8');
          console.log(`Cleaned JS/JSX comments in: ${fullPath}`);
        }
      } else if (file.endsWith('.blade.php')) {
        const content = fs.readFileSync(fullPath, 'utf8');
        const cleaned = cleanBladeScriptTags(content, file);
        if (cleaned !== content) {
          fs.writeFileSync(fullPath, cleaned, 'utf8');
          console.log(`Cleaned Blade script comments in: ${fullPath}`);
        }
      }
    }
  }
}

// Start processing from base directories
const baseDir = 'c:/xampp/htdocs/dailycoffee - Copy - Copy - Copy';
processDirectory(path.join(baseDir, 'resources/js'));
processDirectory(path.join(baseDir, 'resources/views'));
console.log('JS, JSX, and Blade files processed successfully!');
