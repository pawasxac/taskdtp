import babel from '@babel/core';

const code = `
// This is a line comment
const x = 5; /* This is a block comment */
const y = "hello"; // Another line comment
<span>Hello // not a comment</span>
`;

const ast = babel.parseSync(code, {
  filename: 'test.jsx',
  presets: [],
  parserOpts: {
    plugins: ['jsx']
  }
});

console.log(JSON.stringify(ast.comments, null, 2));
