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
