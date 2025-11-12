
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de idioma de validación
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas contienen los mensajes predeterminados de error
    | usados por la clase de validador. Algunas reglas tienen múltiples
    | versiones, como las reglas de tamaño. Siéntete libre de personalizarlas.
    |
    */

    'accepted' => 'Debes aceptar :attribute.',
    'accepted_if' => 'Debes aceptar :attribute cuando :other sea :value.',
    'active_url' => ':attribute no es una URL válida.',
    'after' => ':attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => ':attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => ':attribute solo puede contener letras.',
    'alpha_dash' => ':attribute solo puede contener letras, números, guiones y guiones bajos.',
    'alpha_num' => ':attribute solo puede contener letras y números.',
    'array' => ':attribute debe ser un conjunto.',
    'ascii' => ':attribute solo puede contener caracteres alfanuméricos y símbolos de un solo byte.',
    'before' => ':attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => ':attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'array' => ':attribute debe tener entre :min y :max elementos.',
        'file' => ':attribute debe pesar entre :min y :max kilobytes.',
        'numeric' => ':attribute debe estar entre :min y :max.',
        'string' => ':attribute debe tener entre :min y :max caracteres.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'current_password' => 'La contraseña actual no es correcta.',
    'date' => ':attribute no es una fecha válida.',
    'date_equals' => ':attribute debe ser una fecha igual a :date.',
    'date_format' => ':attribute no corresponde al formato :format.',
    'different' => ':attribute y :other deben ser diferentes.',
    'digits' => ':attribute debe tener :digits dígitos.',
    'digits_between' => ':attribute debe tener entre :min y :max dígitos.',
    'dimensions' => ':attribute tiene dimensiones de imagen no válidas.',
    'distinct' => 'El campo :attribute tiene un valor duplicado.',
    'email' => ':attribute debe ser un correo electrónico válido.',
    'ends_with' => ':attribute debe terminar con uno de los siguientes valores: :values.',
    'exists' => 'El valor seleccionado para :attribute no es válido.',
    'file' => ':attribute debe ser un archivo.',
    'filled' => 'El campo :attribute es obligatorio.',
    'gt' => [
        'array' => ':attribute debe tener más de :value elementos.',
        'file' => ':attribute debe ser mayor que :value kilobytes.',
        'numeric' => ':attribute debe ser mayor que :value.',
        'string' => ':attribute debe tener más de :value caracteres.',
    ],
    'gte' => [
        'array' => ':attribute debe tener :value elementos o más.',
        'file' => ':attribute debe ser mayor o igual a :value kilobytes.',
        'numeric' => ':attribute debe ser mayor o igual a :value.',
        'string' => ':attribute debe tener al menos :value caracteres.',
    ],
    'image' => ':attribute debe ser una imagen.',
    'in' => 'El valor seleccionado para :attribute no es válido.',
    'in_array' => 'El campo :attribute no existe en :other.',
    'integer' => ':attribute debe ser un número entero.',
    'ip' => ':attribute debe ser una dirección IP válida.',
    'ipv4' => ':attribute debe ser una dirección IPv4 válida.',
    'ipv6' => ':attribute debe ser una dirección IPv6 válida.',
    'json' => ':attribute debe ser una cadena JSON válida.',
    'lowercase' => ':attribute debe estar en minúsculas.',
    'lt' => [
        'array' => ':attribute debe tener menos de :value elementos.',
        'file' => ':attribute debe ser menor que :value kilobytes.',
        'numeric' => ':attribute debe ser menor que :value.',
        'string' => ':attribute debe tener menos de :value caracteres.',
    ],
    'lte' => [
        'array' => ':attribute no debe tener más de :value elementos.',
        'file' => ':attribute debe ser menor o igual que :value kilobytes.',
        'numeric' => ':attribute debe ser menor o igual que :value.',
        'string' => ':attribute debe tener como máximo :value caracteres.',
    ],
    'mac_address' => ':attribute debe ser una dirección MAC válida.',
    'max' => [
        'array' => ':attribute no debe tener más de :max elementos.',
        'file' => ':attribute no debe ser mayor que :max kilobytes.',
        'numeric' => ':attribute no debe ser mayor que :max.',
        'string' => ':attribute no debe tener más de :max caracteres.',
    ],
    'mimes' => ':attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => ':attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'array' => ':attribute debe tener al menos :min elementos.',
        'file' => ':attribute debe tener al menos :min kilobytes.',
        'numeric' => ':attribute debe ser al menos :min.',
        'string' => ':attribute debe tener al menos :min caracteres.',
    ],
    'multiple_of' => ':attribute debe ser múltiplo de :value.',
    'not_in' => 'El valor seleccionado para :attribute no es válido.',
    'not_regex' => 'El formato de :attribute no es válido.',
    'numeric' => ':attribute debe ser un número.',
    'password' => [
        'letters' => ':attribute debe contener al menos una letra.',
        'mixed' => ':attribute debe contener al menos una letra mayúscula y una minúscula.',
        'numbers' => ':attribute debe contener al menos un número.',
        'symbols' => ':attribute debe contener al menos un símbolo.',
        'uncompromised' => 'La contraseña proporcionada aparece en una filtración de datos. Elige una diferente.',
    ],
    'present' => 'El campo :attribute debe estar presente.',
    'prohibited' => 'El campo :attribute está prohibido.',
    'regex' => 'El formato de :attribute no es válido.',
    'required' => 'El campo :attribute es obligatorio.',
    'required_if' => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_unless' => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with' => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_without' => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'same' => ':attribute y :other deben coincidir.',
    'size' => [
        'array' => ':attribute debe contener :size elementos.',
        'file' => ':attribute debe tener :size kilobytes.',
        'numeric' => ':attribute debe ser :size.',
        'string' => ':attribute debe tener :size caracteres.',
    ],
    'string' => ':attribute debe ser texto.',
    'unique' => ':attribute ya ha sido registrado.',
    'uploaded' => 'No se pudo subir :attribute.',
    'url' => ':attribute debe ser una URL válida.',
    'uuid' => ':attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Mensajes personalizados
    |--------------------------------------------------------------------------
    */
    'custom' => [
        'password' => [
            'required' => 'Debes ingresar tu contraseña.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nombres de atributos personalizados
    |--------------------------------------------------------------------------
    | Aquí puedes traducir nombres de campos para que los mensajes sean
    | más naturales.
    |
    */
    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
        'current_password' => 'contraseña actual',
        'phone' => 'teléfono',
        'bio' => 'biografía',
        'avatar' => 'foto de perfil',
    ],

];
