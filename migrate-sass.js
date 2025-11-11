#!/usr/bin/env node
const fs = require('fs');
const path = require('path');
const glob = require('glob');

// Get all SCSS files
const scssDir = './gestion_operativa/resources/scss';
const files = glob.sync(`${scssDir}/**/*.scss`);

let updatedCount = 0;

files.forEach(file => {
  try {
    let content = fs.readFileSync(file, 'utf8');
    const originalContent = content;
    
    // Replace @import with @use
    // This regex matches @import statements and replaces them with @use
    content = content.replace(/@import\s+['"]([^'"]+)['"]/g, "@use '$1'");
    
    // Write back if changed
    if (content !== originalContent) {
      fs.writeFileSync(file, content, 'utf8');
      updatedCount++;
      console.log(`✓ Updated: ${file}`);
    }
  } catch (error) {
    console.error(`✗ Error processing ${file}:`, error.message);
  }
});

console.log(`\n✅ Migration complete! ${updatedCount} files updated.`);
