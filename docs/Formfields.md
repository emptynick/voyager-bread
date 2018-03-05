## Formfields
The Formfields have been revamped to be more flexible and do more work.  
A single Formfield has 6 tasks:
#### 1. Generating the `Input`
The input is displayed on `Edit` and `Add` pages  
Passed Variables:
1. `content` The content from the Database
2. `options` the options defined in View-Builder
3. `name` the name of the field
4. `input` true

#### 2. Generating the `Output`
This will be displayed in `Lists` and `Relationships`  
Passed Variables:
1. `data` The input from the Database
2. `multiple` Display multiple results or not
3. `attribute` The attribute to be displayed if `multiple = true`
4. `output` true


#### 3. Generating its `Options`
Options are displayed in the View-Builder  
Passed Variables:
1. `options` The options for this specific Formfield

#### 4. Generating a `Mockup`
The mockup is displayed as a placeholder inside the View-Builder.  
Normally you just want this to be your disabled `Input` view.  
Passed Variables:
1. `options` The options for this specific Formfield
2. `mockup` true

#### 5. Generating the `Content`
This will show the Content for Read-Pages.
Passed Variables:  
1. `data` The input from the Database
2. `render` Render the Content, or return it (bool)
3. `multiple` Display multiple results or not
4. `attribute` The attribute to be displayed if `multiple = true`

#### 6. Parsing its Content
When submitting a form, the method `parse` will be called with the raw `input` data and the `options`.  
The method then parses the content to be written to the Database.

#### Notice
By default, the viewfile is expected to be `bread::formfields.CODENAME.TASK`.  
You can easily set your custom viewfile by adding `protected $viewfile = 'your.file';`.

When calling any of the first five methods, a variable called `input`, `output`, `options`, `mockup` or `content` with value `true` is passed.  
If you want to use just one view-file for your Formfield, you just need to define your view-file and check which variable is set to true.  
## Options
Options can implement the following:
### Activates
Putting the class `activates` to a checkbox/radio will toggle a targets disabled-state defined in the `data-activates` attribute.  
For Example:
```html
<div class="form-group">
	<label>Slug</label>
	<input type="checkbox" class="form-check-input activates" data-activates=".origin">
    <input type="text" class="form-control origin" disabled>
</div>
```
### Repeater
A repeater is applied to a button and will duplicate a given `data-repeats`  
For example
```html
<div class="form-group">
	<label>Options</label>
    <input type="text" class="form-control repeat" name="element[][options][options][][value]">
	<button class="btn btn-primary repeater" data-repeats=".repeat">Add</button>
</div>
```
This will duplicate the `input` and place it after the last occurence of `data-repeats`  
Also you can inject the count of `data-repeats` to an input-value by using `data-inc=".element"`

### Deletable
Deletes the closest `data-delete`.  
For example
```html
<div class="row">
<!-- Content -->
<button class="btn btn-danger deletable" data-delete=".row">Delete</button>
</div>
```
This will delete itself
