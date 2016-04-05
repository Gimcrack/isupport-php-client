<?php

namespace Smarch\Motherbox\Commands;

use File;
use Illuminate\Console\Command;

class PackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:generate
                            {name? : The name of the package.}
                            {vendor? : The vendor name of the package.}
                            {namespace? : Custom Namespace for your package?}
                            {path? : Path for all your package files?}
                            {--C|config= : Include a config file? [yes/no]}
                            {--O|controller= : Include a controller? [yes/no]}
                            {--F|facade= : Include a laravel facade file? [yes/no]}
                            {--M|middleware= : Include a custom middleware file? [yes/no]}
                            {--I|migration= : Include a migration file? [yes/no]}
                            {--D|model= : Include a model file? [yes/no]}
                            {--P|policy= : Include a custom policy file? [yes/no]}
                            {--E|requests= : Include form request validation files in package? [yes/no]}
                            {--R|routes= : Include a routes.php file in package? [yes/no]}
                            {--S|seed= : Include a database seed file? [yes/no]}
                            {--T|test= : Include PhpUnit Test file? [yes/no]}
                            {--table= : Table name for the model. [default is plural of package name]}
                            {--fields= : Fields name for the form(s) & model.}
                            {--fillable= : Fields that should be marked as fillable in the model.}
                            {--guarded= : Fields that should be marked as guarded in the model.}
                            {--pk= : The name of the primary key.}
                            {--author= : Package author name for composer.}
                            {--email= : Package author email for composer.}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a laravel 5.1 composer package';

    /**
     * The composer package name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The composer package vendor
     *
     * @var string
     */
    protected $vendor = '';

    /**
     * The composer full package name (vendor/name)
     *
     * @var string
     */
    protected $package = '';

    /**
     * The composer full package name (vendor/name)
     *
     * @var string
     */
    protected $namespace = '';

    /**
     * The base path to the packages
     *
     * @var string
     */
    protected $path = '';

    /**
     * The path for this package files
     *
     * @var string
     */
    protected $packagePath = '';

    /**
     * The path for this package src files
     *
     * @var string
     */
    protected $srcPath = '';

    /**
     * Command options that make files.
     *
     * @var array
     */
    protected $makeFiles = ['config','controller','facade','middleware','migration','model','policy','requests','routes','seed','test'];

    /**
     * Stub strings to replace.
     *
     * @var array
     */
    protected $searchWords = ['{{package}}','{{vendor}}','{{name}}','{{capVendor}}','{{capName}}','{{namespace}}','{{capNamespace}}'];

    /**
     * Stub replacement strings.
     *
     * @var array
     */
    protected $replaceWords = [];

    /**
     * The model table name.
     *
     * @var string
     */
    protected $table = '';

    /**
     * The model fillable fields.
     *
     * @var string
     */
    protected $fillable = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // empty variables needed for stubs string replacements
        $this->indexHeaders = $this->indexFields = $this->storeFields = $this->updateFields = '';
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->makeVendorName( strtolower( $this->argument('vendor') ), strtolower( $this->argument('name') ) );
        $this->makeNamespace();
        $this->makePaths();

        if ( File::isDirectory($this->packagePath) ) {
            $this->error("Package already exists!");
            if (! $this->confirm("Please confirm you would like to overwrite this package.") ) {
                $this->line("Halted!");
                return;
            }
            if (File::deleteDirectory($this->packagePath) ){
                $this->line('<info>Deleted: </info>Previous existing package directory.');        
                $this->line('----------------------------------');
            } else {
                $this->error('Unable to delete previous package directory.');
            }
        }

        $this->line('<info>Root Packages Path: </info> '.$this->path);
        $this->line('<info>Package Path: </info> '.$this->packagePath);
        $this->line('<info>PSR4-Src Path: </info> '.$this->srcPath);
        $this->line('<info>Stubs Path: </info> '.$this->stubPath);
        $this->line('----------------------------------');

        $this->getOptions();

        $this->table = ($this->table) ?: str_plural($this->name);
        $this->pk = ($this->pk) ?: 'id';
        $this->makeSchema();

        $this->makeReplaceWords();
        $this->makeDirectory($this->path);
        $this->makeDirectory($this->packagePath);

        $this->makeFile('composer.json.stub', 'composer.json', ['{{author}}','{{email}}', '{{psrNamespace}}'], [$this->author,$this->email,str_replace('\\','\\\\',$this->capNamespace)]);
        $this->makeFile('LICENSE.stub', 'LICENSE', ['{{author}}'], [$this->author]);



        $this->makeOptions();
        $this->makeViews();

        $this->makeServiceProvider();
    }


    protected function makeVendorName($v='',$n='',$alreadyChecked=false)
    {
        $vendor = ($v) ?: ( config('motherbox.vendor') ?: $this->ask('Name of the vendor for your composer package?') );
        $name = ($n) ?: ( config('motherbox.name') ?: $this->ask('Name of your composer package?') );

        if ($alreadyChecked) {
            $vendor = $this->ask('Name of the vendor for your composer package?');
            $name = $this->ask('Name of your composer package?');
        }

        $this->vendor = strtolower($vendor);
        $this->name = strtolower($name);

        $this->package = strtolower($this->vendor . '/'. $this->name);
        $this->capVendor = ucwords($this->vendor);
        $this->capName = ucwords($this->name);
        
        if ($this->confirm("Please confirm you would like this vendor/name combination : " . $this->package)) {
            return;
        }

        $this->makeVendorName('','',true);        
    }


    protected function makeNamespace()
    {
        $this->namespace = ($this->argument('namespace')) ?:  ( config('motherbox.namespace') ?: ucwords($this->vendor) . '\\'. ucwords($this->name) );
        $this->capNamespace = implode( '\\', array_map('ucwords', explode('\\',$this->namespace) ) );
    }


    protected function makePaths()
    {
        $this->path = base_path() . '/' . ( ( $this->argument('path') ) ?: ( config('motherbox.path') ?: $this->package ) );
        $this->packagePath = $this->path . '\\' . $this->package;
        $this->srcPath = $this->packagePath . '/src';
        $this->stubPath = config('motherbox.stub_path') ?: __DIR__.'/../stubs';
        if ( ! File::isDirectory($this->stubPath) ) {
            if (! File::isDirectory($this->stubPath = __DIR__.'/../stubs') ) {
                $this->error('No stub files found at specified stub path : '.$this->stubPath);
                exit;
            }
        }
    }


    protected function getOptions()
    {
        foreach($this->option() as $k => $v) {
            $this->$k = ($v) ?: config('motherbox.'.$k);
        }
    }


    protected function makeReplaceWords()
    {
        $this->replaceWords = [$this->package, $this->vendor, $this->name, $this->capVendor, $this->capName, $this->namespace, $this->capNamespace];
    }


    protected function makeOptions()
    {
        $options = $this->option();
        foreach($options as $k => $v) {
            if ( in_array($k, $this->makeFiles) && ($v === "yes" OR $this->$k ==="yes") ) {
                $option = ucfirst($k);
                $com = 'make'.$option;
                $this->$com();
            }
        }        
    }


    protected function makeDirectory($path)
    {        
        if ( ! File::isDirectory($path) ) {
            File::makeDirectory($path, 0755, true);
        }
    }


    protected function makeFile($stub, $file, $search=[], $replace=[], $folder='')
    {
        $s = array_merge($this->searchWords, $search);
        $r = array_merge($this->replaceWords, $replace);

        $path = $this->packagePath;
        if ($folder) {
            $path = $this->srcPath . '/' . $folder;
            $this->makeDirectory($path);    
        }
     
        $stubFile = $this->stubPath . '/' . $stub;
        $newFile = $path . '/' . $file;

        if ( File::copy($stubFile, $newFile)) {
            if ( File::put($newFile, str_replace($s, $r, File::get($newFile) ) ) ){
                $this->line("<info>Created:</info> $folder/$file");
            } else {
                $this->line("<error>Error Creating:</error> $folder . '/' . $file");
            }
        } else {
            $this->error('Unable to copy "' . $stub . '" to "' . $folder . '/' . $file . '"');
        }
    }


    protected function makeConfig() 
    {
        $this->makeFile('config.stub', $this->name.'.php', [], [], 'Config');
    }


    protected function makeServiceProvider() 
    {
        $this->makeFile('provider.stub', 'src/'.$this->capName.'ServiceProvider.php');
    }


    protected function makeController() 
    {
        $ns = $this->laravel->getNamespace(); 
        $np = str_plural($this->name); 

        $this->makeFile('controller.stub', $this->capName.'Controller.php', ['{{rootNamespace}}', '{{lcNamePlural}}'], [$ns, $np], 'Controllers');
    }


    protected function makeFacade() 
    {
        $this->makeFile('facade.stub', $this->capName.'Facade.php', [], [], 'Facades');
    }


    protected function makeMiddleware() 
    {
        $this->makeFile('middleware.stub', $this->capName.'Middleware.php', [], [], 'Middleware');
    }


    protected function makeMigration() 
    {
        $this->table = ($this->table) ?: str_plural($this->name);
        $this->pk = ($this->pk) ?: 'id';
        $name = date('Y_m_d_His') . '_create_'.$this->table.'_table.php';
        $this->makeFile('migration.stub', $name, ['{{capTable}}','{{table}}', '{{pk}}', '{{fields}}'], [ ucfirst($this->table), $this->table, $this->pk,$this->schema], 'Migrations');
        $this->call('migrate',['--path'=>'packages\smarch\\'.$this->name.'\src\Migrations']);
    }


    protected function makeModel() 
    {
        $this->makeFile('model.stub', $this->capName . '.php', ['{{table}}', '{{fillable}}', '{{pk}}'], [$this->table, substr($this->fillable,0,-1), $this->pk], 'Models');
    }


    protected function makePolicy() 
    {
        $this->makeFile('policy.stub', $this->capName.'Policy.php', [], [], 'Policies');
    }


    protected function makeRequests() 
    {
        $this->makeFile('storeRequest.stub', 'StoreRequest.php', ['{{requestFields}}'], [ substr($this->storeFields,0,-1) ], 'Requests');
        $this->makeFile('updateRequest.stub', 'UpdateRequest.php', ['{{requestFields}}'], [ substr($this->updateFields,0,-1) ], 'Requests');
    }


    protected function makeRoutes() 
    {
        $this->makeFile('routes.stub', 'src/routes.php');
    }


    protected function makeSeed() 
    {
        $this->makeFile('seeder.stub', ucfirst($this->table).'TableSeeder.php', ['{{table}}'], [$this->table], 'Seeds');
    }


    protected function makeTest() 
    {
        $this->makeFile('test.stub', $this->capName.'.php', [], [], 'Tests');
    }

    protected function verifyEmptyFields()
    {   
        if ( $this->confirm("<error>No fields specified.</error> Is this correct?") ) {
            return true;
        }

        $this->fields = $this->ask("Add your fields now. Seperate each by a comma. Format is 'type:name:options',");
    }


    protected function makeSchema()
    {
        $this->schema = '';
        $this->formFields = '';

        if ( empty($this->fields) ) {
            if ( $this->verifyEmptyFields() ) {
                return;
            }
        }

        $result = '';
        $fields = explode(',',$this->fields);
        $j = 0;
        foreach($fields as $field) {
            $name = $type = $required = $unique = $uniqueUpdate = $require = $opts = $ret = '';

            if (! stristr($field,":") ) {
                // probably just provided the name, make it a string.
                $field = 'string:'.$field;
            }
            
            $bits = explode(':', $field);
            $type = trim($bits[0]);
            $name = trim($bits[1]);
            $result .= "\t\t\t".'$table->'.$type."('".$name."')";
            for($i=2;$i<count($bits);$i++) {
                $result .='->'.$bits[$i].'()';
                if ($bits[$i] === 'required') {
                    $required = ", 'required' => 'required'";
                    $require = 'required|';
                }

                if ($bits[$i] === 'unique') {
                    $unique = 'unique:'. $this->table .'|';
                    $uniqueUpdate = substr($unique,0,-1) . ",".$name.',\'.$this->get("'.$this->pk."\").'|";
                }
            }

            $result .= ";\n";

            $this->fillable .= "'".$name."',";

            $this->formFields .= $this->makeField($name, $type, $required);

            $ret = str_replace('views/','', str_replace('.field.stub', '', $this->getFieldStub($type) ) );
            $opts = "alpha_dash|min:2|max:255";
            if ($ret != 'text')     $opts = $ret; 
            if ($ret == 'textarea') $opts = 'string';           
            if ($name == 'email')   $opts = 'email';

            $this->storeFields .= "\n\t\t\t'".$name."' => '". $require . $unique . $opts . "',";
            $this->updateFields .= "\n\t\t\t'".$name."' => '". $require . $uniqueUpdate . $opts . "',";
            
            if ($j <= 2 ) {
                $j++;
                $this->indexHeaders .= "\t<th>".$name."</th>\n";
                $html = '<td class="hidden-xs hidden-sm">{{ $item->'.$name.' }}</td>';
                if ($j == 0) {
                    $html = '<td>
                      <a href="{{ url('.$this->name.', $item->id) }}">{{ $item->'.$name.' }}</a>
                    </td>';
                }
                $this->indexFields .= $html;
            }
        }

        $this->schema = substr($result,0,-1);
    }


    protected function makeViews()
    {
        $this->makeFile('views\create.blade.stub', 'create.blade.php', ['{{formFields}}'], [trim($this->formFields)], 'Views');
        $this->makeFile('views\edit.blade.stub', 'edit.blade.php', ['{{formFields}}'], [trim($this->formFields)], 'Views');
        $this->makeFile('views\index.blade.stub', 'index.blade.php', ['{{indexHeaders}}','{{indexFields}}'], [$this->indexHeaders,$this->indexFields], 'Views');
    }


    protected function makeField($name, $type, $required='', $search=[], $replace=[] )
    {
        $s = array_merge($this->searchWords, [ '{{fName}}', '{{fcapName}}', '{{required}}' ] );
        $r = array_merge($this->replaceWords, [ $name, ucfirst($name), $required ] );

        $stub = $this->getFieldStub($type);
        $fieldStubFile = $this->stubPath . '/' . $stub;

        return str_replace($s, $r, File::get($fieldStubFile) );
    }


    protected function getFieldStub($type)
    {
        switch ($type) {
            case 'bigInteger':
            case 'decimal':
            case 'double':
            case 'float':
            case 'integer':
            case 'mediumInteger':
            case 'smallInteger':
            case 'tinyInteger':
                $result = 'numeric';
                break;
            
            case 'binary':
            case 'longtext':
            case 'mediumtext':
                $result = 'textarea';
                break;

            case 'boolean':
                $result = 'boolean';
                break;

            case 'date':
            case 'dateTime':
            case 'time':
            case 'timestamp':
            //    $result = 'dateTime';
            //    break;

            case 'json':
            case 'jsonb':
            //    $result = 'json';
            //    break;

            case 'uuid':
            //    $result = 'uuid';
            //    break;

            case 'char':
            case 'string':
            case 'text':           
            default:
                $result = 'text';
                break;
        }

        return 'views/'. $result .'.field.stub';
    }
}