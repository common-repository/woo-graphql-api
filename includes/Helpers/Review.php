<?php

namespace WCGQL\Helpers;

class Review
{
    public static function addReview($product_id, $review)
    {
        $query = array('post_type' => 'product', 'post_id' => $product_id, 'user_id' => $review['user_id']);
        $revs = get_comments($query);
        if ($revs) {
            throw new ClientException('You can comment once for each product');
        }

        $comment = array(
            'comment_post_ID' => $product_id,
            'comment_author' => $review['name'],
            'comment_author_url' => '',
            'comment_author_email' => $review['email'],
            'comment_type' => '',
            'comment_content' => $review['text'],
            'user_id' => $review['user_id'],
        );

        $comment_id = \wp_new_comment($comment);
        \add_comment_meta($comment_id, 'rating', $review['rating']);

        return $comment_id;
    }

    public static function getReviews($product_id)
    {
        $reviews = self::getOnlyApprovedReviews($product_id);
        return array_map(function ($review) {
            $isPurchaseVerified = wc_customer_bought_product(
                $review->comment_author_email,
                $review->user_id,
                $review->comment_post_ID
            );

            $commentMeta = \get_comment_meta($review->comment_ID);
            $rating  = isset($commentMeta['rating'])? reset($commentMeta['rating']): 0;

            return array(
                'review_id' => $review->comment_ID,
                'author' => $review->comment_author,
                'email' => $review->comment_author_email,
                'text' => $review->comment_content,
                'createdAt' => $review->comment_date,
                'rating' => $rating,
                'purchase_verified' => $isPurchaseVerified,
            );
        }, $reviews);
    }

    public static function getOnlyApprovedReviews($product_id)
    {
        $query = array(
            'post_type' => 'product',
            'post_id' => $product_id,
            'status' => 'approve',
        );
        return get_comments($query);
    }
}
