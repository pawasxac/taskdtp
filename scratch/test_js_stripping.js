import babel from '@babel/core';
import fs from 'fs';

const code = `
// This is a line comment
const x = 5; /* This is a block comment */
const y = "hello"; // Another line comment
<span>Hello // not a comment</span>
`;

const ast = babel.parseSync(code, {
  filename: 'test.jsx',
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
const finalResult = cleanedLines.join('\n');

console.log("Original:\n", code);
console.log("------------------------");
console.log("Cleaned:\n", finalResult);
