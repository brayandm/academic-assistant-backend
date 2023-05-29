<?php

namespace App\GraphQL\Directives;

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
                requiredRoles: [UsersRoles!]!
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

            $requiredRoles = $this->directiveArgValue('requiredRoles');

            $allowedRoles = ['STUDENT', 'TEACHER', 'ADMIN'];

            foreach ($requiredRoles as $role) {
                if (! in_array($role, $allowedRoles)) {
                    throw new Exception('Invalid role provided: ' . $role);
                }
            }

            $userRoles = $user->roles->pluck('name')->toArray();

            foreach ($requiredRoles as $role) {
                if (! in_array($role, $userRoles)) {
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
