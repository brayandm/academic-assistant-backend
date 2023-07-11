<?php

namespace App\GraphQL\Directives;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Auth\AuthenticationException;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Facades\RequestManagerFacades;
use Exception;

class RateLimitDirective extends BaseDirective implements FieldMiddleware
{
    public static function definition(): string
    {
        return
            /** @lang GraphQL */
            <<<'GRAPHQL'
            """
            Limit requests per some amount of seconds.
            """
            directive @rateLimit(
                """
                The maximum number of requests.
                """
                rate: Int!
                """
                The number of seconds before the limit resets.
                """
                seconds: Int!
            ) on ARGUMENT_DEFINITION | INPUT_FIELD_DEFINITION
            GRAPHQL;
    }

    public function handleField(FieldValue $fieldValue): void
    {
        // If you have any work to do that does not require the resolver arguments, do it here.
        // This code is executed only once per field, whereas the resolver can be called often.

        $fieldValue->wrapResolver(fn (callable $resolver) => function (mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) use ($resolver) {
            // Do something before the resolver, e.g. validate $args, check authentication

            if (! $this->directiveArgValue('rate') || ! $this->directiveArgValue('seconds')) {
                throw new Exception('You must provide a rate and seconds argument in the graphql schema');
            }

            if(! RequestManagerFacades::rateLimit($resolveInfo->fieldName,
                $this->directiveArgValue('rate'),
                $this->directiveArgValue('seconds'))) {
                throw new AuthenticationException('Too many requests');
            }

            // Call the actual resolver
            $result = $resolver($root, $args, $context, $resolveInfo);

            // Do something with the result, e.g. transform some fields

            return $result;
        });
    }
}
