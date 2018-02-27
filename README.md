# Voyager Bread Builder
This is a new Package which allows you to build extensive BREAD Views and Lists.  
## Installation
#### 1. Clone the hook
From your root directory clone the hook into the hooks dir
```bash
git clone https://github.com/emptynick/bread.git hooks/bread
```
#### 2. Install the hook
After you cloned the hook, you can install it.
```bash
php artisan hook:install bread
```
#### 3. Enable the hook
You can do this through Voyagers Hook-UI or by running the following command
```bash
php artisan hook:enable bread
```
## Terminology
##### View
A View is a set of Formfields. They are used for Reading, Editing and Adding BREAD-Content.  
A View can contain the Relationships from its model.
##### List
A List is used for Browsing BREAD-Content and displaying Relationships in a View.  
It can contain its own fields, relationship fields and pivot fields.  
In the List-Builder you can set 3 different options:
- **Searchable** Sets if this field is searchable (Global search **and** single-column-search)
- **Orderable** Sets if this field is orderable. Please read the note [here](/docs/Relationships.md)
- **Visible** Sets if this field is visible. Useful if you want to search a field but don't want to display it.

Both Views and Lists can have a free-name (and get assigned to different Roles)


## Read more about
- [Relationships](/docs/Relationships.md)
- [Formfields](/docs/Formfields.md)

## Todo:
- [ ] Assign views to Roles *or* create a Permission for each view
- [ ] Validate relationship
- [ ] Implement various FormFields
- [ ] Add `disabled` option to Formfields
- [ ] Change the way we get the Columns.  
	Currently we read the Database, because there is no way getting the attributes through the model if theres no entry.
