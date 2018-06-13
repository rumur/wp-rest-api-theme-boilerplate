<?php

namespace App\Api\Services\Validation;

use function App\{app, config};

use App\Api\Services\Http\Request;

use Illuminate\Validation\Factory;
use Illuminate\Filesystem\Filesystem;
use \Illuminate\Validation\Validator;
use Illuminate\Translation\{FileLoader, Translator};

/**
 * Trait ValidationTrait
 * @package App\Api\Services\Validation
 */
trait ValidationTrait {
	/** @var Validator */
	protected $validator;

	/**
	 * Validate the fields.
	 *
	 * @param array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
	 *
	 * @uses  \Filesystem::class to get the `lang` folder.
	 * @uses  \FileLoader::class to load the file for messages.
	 * @uses  \Translator::class determine the messages for specific lang.
	 * @uses  \Factory::class to make validation.
	 *
     * @return Validator
	 *
	 * @author rumur
	 */
    public function validate(array $rules, array $messages = [], array $customAttributes = [])
	{
        /** @var Request $request */
        $request = $this->request;
        $loader = new FileLoader(new Filesystem, config('theme.lang.path'));

        $translator = new Translator($loader,'/');
        $validator = new Factory($translator, app());

        if ($presence = app('app.validation.presence')) {
            $validator->setPresenceVerifier($presence);
        }

        return $this->validator = $validator->make($request->all(), $rules, $messages, $customAttributes);
	}
}
