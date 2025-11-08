<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $rfc
 * @property string $curp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentApplication whereCurp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentApplication whereRfc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentApplication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentApplication whereUserId($value)
 */
	class AgentApplication extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $amenity_category_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AmenityCategory $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Property> $properties
 * @property-read int|null $properties_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity whereAmenityCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amenity whereUpdatedAt($value)
 */
	class Amenity extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Amenity> $amenities
 * @property-read int|null $amenities_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AmenityCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AmenityCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AmenityCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AmenityCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AmenityCategory whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AmenityCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AmenityCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AmenityCategory whereUpdatedAt($value)
 */
	class AmenityCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property string $location
 * @property string $price
 * @property string $type
 * @property string $status
 * @property string $listing_type
 * @property string|null $latitude
 * @property string|null $longitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Amenity> $amenities
 * @property-read int|null $amenities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $favoritedBy
 * @property-read int|null $favorited_by_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PropertyImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereListingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Property whereUserId($value)
 */
	class Property extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $property_id
 * @property string $image_path
 * @property int $is_featured
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Property $property
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyImage whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyImage whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyImage wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PropertyImage whereUpdatedAt($value)
 */
	class PropertyImage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $property_id
 * @property int $user_id
 * @property int $rating
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Property $property
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUserId($value)
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $phone
 * @property string|null $avatar
 * @property string|null $bio
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Property> $favorites
 * @property-read int|null $favorites_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Property> $properties
 * @property-read int|null $properties_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Visit> $visitsAsAgent
 * @property-read int|null $visits_as_agent_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Visit> $visitsAsClient
 * @property-read int|null $visits_as_client_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $client_id
 * @property int $agent_id
 * @property int $property_id
 * @property \Illuminate\Support\Carbon $visit_date
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $agent
 * @property-read \App\Models\User $client
 * @property-read \App\Models\Property $property
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visit whereVisitDate($value)
 */
	class Visit extends \Eloquent {}
}

