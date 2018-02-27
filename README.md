# Voyager Bread Builder
Voyager hook which allows you to build extensive BREAD Views and Lists.  
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
### 4. Optional: Add Dummy Data
To get you started, you can run
```bash
php artisan bread:dummydata
```
This will create some views, lists and models for `Categories`, `Pages` and `Posts`
## Concepts
This Hook works different from how you are used to in Voyager.  
In Voyager you assign a Formfield to a Field, so you have a fixed amount of elements.  
But in Lists or Views you can have as many Formfields as you want and assign a Field to it.  
Remember that it doesn't care if it makes sense (e.g. you have the same Field assigned to three Formfields)  

A BREAD-Type can have many Lists and Views.  
With this you can create different Views for different roles, e.g. an `Admin` can have a different View than a `Writer`  

#### View
A View is a set of Formfields. They are used for Reading, Editing, Viewing and Adding BREAD-Content.  
Views can contain the Relationships from its model.  

#### List
A List is used for Browsing BREAD-Content and displaying Relationships in a View.  
It can contain its own fields, relationship fields and pivot fields.

Both Views and Lists can have a free-name (~~and get assigned to different Roles~~)


## Read more about
- [Relationships](/docs/Relationships.md)
- [Formfields](/docs/Formfields.md)

## Todo:
- [ ] Assign views to Roles *or* create a Permission for each view
- [ ] Validate relationship
- [ ] Implement various FormFields
- [ ] Add `disabled` option to Formfields
- [ ] Reimplement Permissions
- [ ] Dont display a relationship as a DataTable if there is only one `visible_row`
- [ ] Properly display a relationship in `Read`
- [ ] Add Validation-Repeater to List-Builder
- [ ] Change the way we get Model-Fields.  
	Currently we read the Database, because there is no way getting the attributes through the model if theres no entry.

## Bugs:
- Adding a new entry through a relationship does not save the relationship
