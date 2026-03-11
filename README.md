# poemei.com
Built upon the Chaos MVC V1

# What
Various changes pertaining to my domain only.

# Why
Cause I can.

# Who
Me = Poe Mei = a Transgender Female Content and micro OF creator
(micro OF = No nudity)

# Where
https://www.poemei.com

## Markdown Rendering
In `/app/lib/render_md.php`
 - Loaded from `/app/bootstrap.php`->`autoload()`
   - Links `[text](url)`
   - Headings: `#..######` (space optional)
   - Bold: `**text**`
   - Small: `~~text~~`
   - Inline code: `code`
   - Fenced code: ```php / ```json / ```go / etc.
   - Blockquotes: `> text` (multi-line)
   - Unordered lists: `-, *, +`
   - Ordered lists: `1. 2. 3.`
   - Newlines preserved outside `<pre>`.
- **Usage**: 
```php
<?php
$text = "
# Hi
This is just a test `Markdown` message.
 - You should **echo** this.
 ";
```
```php
<?php
echo $this->render_md->markdown($text);
```
Will render as:
# Hi
This is just a test `Markdown` message.
 - You should **echo** this.
 ***

## To Do
**Core**
- [x] Admin
- [ ] SEO Automation
- [x] Posts
- [x] Media

**Not Core**
- [ ] Monetization
- [ ] Web3 Blockchain Integrations

