<?php

namespace App\GraphQL\Directives;

use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Redis;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RateLimitDirective extends BaseDirective implements FieldMiddleware
{
    public static function definition(): string
    {
        return
            /** @lang GraphQL */
            <<<'GRAPHQL'
            """
            Limit requests per minute for the current endpoint.
            """
            directive @rateLimit(
                """
                The maximum number of requests per minute.
                """
                rpm: Int!
            ) on ARGUMENT_DEFINITION | INPUT_FIELD_DEFINITION
            GRAPHQL;
    }

    public function handleField(FieldValue $fieldValue): void
    {
        // If you have any work to do that does not require the resolver arguments, do it here.
        // This code is executed only once per field, whereas the resolver can be called often.

        $fieldValue->wrapResolver(fn (callable $resolver) => function (mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) use ($resolver) {
            // Do something before the resolver, e.g. validate $args, check authentication

            $user = $context->user();

            if (! $user) {
                throw new AuthenticationException('Unauthenticated');
            }

            $key = $resolveInfo->fieldName . ':' . $user->id . ':' . Carbon::now()->format('i');

            if(Redis::setnx($key, 0)) {
                Redis::expire($key, 120);
            }

            if(Redis::incr($key, 1) > $this->directiveArgValue('rpm')) {
                throw new AuthenticationException('Too many requests');
            }

            // Call the actual resolver
            $result = $resolver($root, $args, $context, $resolveInfo);

            // Do something with the result, e.g. transform some fields

            return $result;
        });
    }
}
