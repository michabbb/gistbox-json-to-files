# gistbox-json-to-files
Extract group snippets from gistbox json backup to individual files.  
For every private gistbox-group a directory will be created and inside this dir you will find  
all your gists as a markdown file with the following content, example:


# my-snippet.php  

id: `124124g241242`  
label: `helper`,`linux`  
createdAt: `2015-06-08T22:20:07Z`  
updatedAt: `2015-06-08T22:20:07Z`  
  
  
```php
<?php
the contents of your snippet
```

**Call the script**

`php export.php <your json gistbox backup file> <your export path>`  

Example:  
  
`php export.php /tmp/gistbox_snippets.json /tmp/`
  
**Where do i get the json export of my gistbox gists?**  
While doing the migration from gistbox to cacher, you will be asked to save your gists  
and download everything as a json file.
