<?php

namespace App\GraphQL\Directives;

use App\Facades\RequestManagerFacades;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Auth\AuthenticationException;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ThreadLimitDirective extends BaseDirective implements FieldMiddleware
{
    public static function definition(): string
    {
        return
            /** @lang GraphQL */
            <<<'GRAPHQL'
            """
            Limit the number of pending requests for a user.
            """
            directive @threadLimit(
                """
                The name of the task.
                """
                task: String!
                """
                The maximum number of threads.
                """
                threads: Int!
                """
                The number of seconds after which the thread limit will expire.
                """
                expiration: Int!
            ) on ARGUMENT_DEFINITION | INPUT_FIELD_DEFINITION
            GRAPHQL;
    }

    public function handleField(FieldValue $fieldValue): void
    {
        // If you have any work to do that does not require the resolver arguments, do it here.
        // This code is executed only once per field, whereas the resolver can be called often.

        $fieldValue->wrapResolver(fn (callable $resolver) => function (mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) use ($resolver) {
            // Do something before the resolver, e.g. validate $args, check authentication

            if (! $this->directiveArgValue('task') || ! $this->directiveArgValue('threads') || ! $this->directiveArgValue('expiration')) {
                throw new Exception('You must provide a \'task\', \'threads\' and \'expiration\' argument in the graphql schema');
            }

            if (! RequestManagerFacades::threadLimit($this->directiveArgValue('task'),
                $this->directiveArgValue('threads'),
                $this->directiveArgValue('expiration'))) {
                throw new AuthenticationException('Too many threads');
            }

            // Call the actual resolver
            $result = $resolver($root, $args, $context, $resolveInfo);

            // Do something with the result, e.g. transform some fields

            return $result;
        });
    }
}
