<?php

namespace App\GraphQL\Directives;

use App\Models\Policy;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Auth\AuthenticationException;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CanAccessDirective extends BaseDirective implements FieldMiddleware
{
    public static function definition(): string
    {
        return
            /** @lang GraphQL */
            <<<'GRAPHQL'
            """
            Limit access to users of a certain role.
            """
            directive @canAccess(
                """
                The name of the role authorized users need to have.
                """
                requiredPolicies: [UserPolicies!]!
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

            $requiredPolicies = $this->directiveArgValue('requiredPolicies');

            $allowedPolicies = Policy::all()->pluck('name')->toArray();

            foreach ($requiredPolicies as $policy) {
                if (! in_array($policy, $allowedPolicies)) {
                    throw new Exception('There are invalid policies declared in the schema');
                }
            }

            $userPolicies = $user->policies->pluck('name')->toArray();

            foreach ($requiredPolicies as $policy) {
                if (! in_array($policy, $userPolicies)) {
                    throw new AuthenticationException('Unauthorized');
                }
            }

            // Call the actual resolver
            $result = $resolver($root, $args, $context, $resolveInfo);

            // Do something with the result, e.g. transform some fields

            return $result;
        });
    }
}
