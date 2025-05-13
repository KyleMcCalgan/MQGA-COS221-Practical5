import requests
import time
import json
import random
from collections import defaultdict

def build_diverse_dataset(search_configs, total_books_needed, api_key=None):
    """
    Creates a diverse dataset of books with enhanced diversity controls.
    
    Args:
        search_configs: List of dictionaries with search query and diversity category
        total_books_needed: Total number of unique books to collect
        api_key: Optional Google Books API key
    
    Returns:
        List of exactly the requested number of unique books with balanced diversity
    """
    all_books = []
    seen_ids = set()  # To track unique book IDs
    
    # Track diversity metrics
    diversity_counts = defaultdict(int)
    
    # Shuffle search configs to avoid bias towards earlier terms
    random.shuffle(search_configs)
    
    # Calculate initial books per configuration
    books_per_config = max(1, total_books_needed // len(search_configs))
    
    for config in search_configs:
        term = config['query']
        category = config['category']
        language = config.get('language', 'any')
        
        term_books = []
        unique_term_books = 0
        
        print(f"Fetching books for search term: '{term}' (Category: {category}, Language: {language})")
        
        # Keep fetching until we have enough unique books for this term
        start_index = 0
        max_attempts = 5  # Limit attempts per term to avoid endless loops
        attempts = 0
        
        while unique_term_books < books_per_config and attempts < max_attempts:
            attempts += 1
            
            # Build API request
            params = {
                'q': term,
                'startIndex': start_index,
                'maxResults': 40  # Max allowed per request
            }
            if api_key:
                params['key'] = api_key

            try:
                response = requests.get('https://www.googleapis.com/books/v1/volumes', params=params)
                
                if response.status_code != 200:
                    print(f"Request failed with status code {response.status_code}")
                    break

                data = response.json()

                if 'items' not in data or not data['items']:
                    print(f"No more items found for '{term}'.")
                    break

                for item in data['items']:
                    book_data = parse_book(item)
                    
                    # Only add if it's a new unique book
                    if book_data['id'] not in seen_ids:
                        # Apply diversity filters for languages if specified
                        if language != 'any' and book_data['language'] != language:
                            continue
                            
                        seen_ids.add(book_data['id'])
                        book_data['diversity_category'] = category  # Tag with diversity category
                        term_books.append(book_data)
                        unique_term_books += 1
                        diversity_counts[category] += 1
                        
                        # Break once we have enough for this term
                        if unique_term_books >= books_per_config:
                            break
                
                # Move to next page of results
                start_index += len(data['items'])
                
                # Check if we've reached the end of available results
                if len(data['items']) < params['maxResults']:
                    break
                    
                time.sleep(0.5)  # Short pause for rate limits
                
            except Exception as e:
                print(f"Error processing term '{term}': {str(e)}")
                break
        
        print(f"Got {len(term_books)} unique books from term '{term}'")
        all_books.extend(term_books)
        
        # Print current diversity stats
        print("Current diversity distribution:")
        for cat, count in diversity_counts.items():
            print(f"  - {cat}: {count} books ({count/len(all_books)*100:.1f}%)")
        
        # Adjust targets for remaining terms to balance categories
        if len(all_books) < total_books_needed:
            remaining_configs = len(search_configs) - search_configs.index(config) - 1
            if remaining_configs > 0:
                remaining_needed = total_books_needed - len(all_books)
                books_per_config = max(1, remaining_needed // remaining_configs)
    
    # Balance final dataset if possible
    print("\nFinal diversity distribution before balancing:")
    for cat, count in diversity_counts.items():
        print(f"  - {cat}: {count} books ({count/len(all_books)*100:.1f}%)")
    
    # If we have extra books, trim to exactly the requested amount while maintaining diversity
    if len(all_books) > total_books_needed:
        # Sort by most overrepresented categories
        category_percentages = {cat: count/len(all_books) for cat, count in diversity_counts.items()}
        all_books.sort(key=lambda x: category_percentages[x['diversity_category']], reverse=True)
        all_books = all_books[:total_books_needed]
    
    # Final diversity check
    final_counts = defaultdict(int)
    for book in all_books:
        final_counts[book['diversity_category']] += 1
    
    print("\nFinal diversity distribution:")
    for cat, count in final_counts.items():
        print(f"  - {cat}: {count} books ({count/len(all_books)*100:.1f}%)")
    
    return all_books

def parse_book(item):
    """Extract book fields and add random ratings data."""
    volume_info = item.get('volumeInfo', {})
    sale_info = item.get('saleInfo', {})
    access_info = item.get('accessInfo', {})

    # Get ISBN13 if available
    isbn13 = None
    for identifier in volume_info.get('industryIdentifiers', []):
        if identifier.get('type') == 'ISBN_13':
            isbn13 = identifier.get('identifier')
            break

    # Generate random ratings data
    random_ratings_count = random.randint(5, 2500)
    random_average_rating = round(random.uniform(2.5, 5.0), 1)
    
    # Extract smallThumbnail and thumbnail directly from imageLinks
    image_links = volume_info.get('imageLinks', {})
    small_thumbnail = image_links.get('smallThumbnail')
    thumbnail = image_links.get('thumbnail')

    book = {
        'id': item.get('id'),
        'title': volume_info.get('title'),
        'author': (volume_info.get('authors') or [None])[0],  # first author
        'publisher': volume_info.get('publisher'),
        'publishedDate': volume_info.get('publishedDate'),
        'description': volume_info.get('description'),
        'categories': volume_info.get('categories', []),
        'pageCount': volume_info.get('pageCount'),
        'maturityRating': volume_info.get('maturityRating'),
        'language': volume_info.get('language'),
        'smallThumbnail': small_thumbnail,  # Add as top-level key
        'thumbnail': thumbnail,  # Add as top-level key
        'accessibleIn': access_info.get("country"),
        'ratingsCount': random_ratings_count,
        'isbn13': isbn13,
    }

    return book

# Example usage
if __name__ == "__main__":
    # Enhanced search configurations with diversity categories
    SEARCH_CONFIGS = [
        # Western languages with specific categories
        {"query": "book subject:fiction", "category": "western_fiction", "language": "en"},
        {"query": "book subject:nonfiction", "category": "western_nonfiction", "language": "en"},
        {"query": "book subject:science", "category": "western_science", "language": "en"},
        {"query": "book subject:philosophy", "category": "western_philosophy", "language": "en"},
        
        # Non-Western languages literature 
        {"query": "libro literatura", "category": "spanish_literature", "language": "es"},
        {"query": "livre littérature", "category": "french_literature", "language": "fr"},
        {"query": "книга литература", "category": "russian_literature", "language": "ru"},
        {"query": "書籍 文学", "category": "japanese_literature", "language": "ja"},
        {"query": "图书 文学", "category": "chinese_literature", "language": "zh"},
        {"query": "kitab adab", "category": "arabic_literature", "language": "ar"},
        
        # Regional focuses
        {"query": "african literature", "category": "african_literature", "language": "any"},
        {"query": "latin american literature", "category": "latinamerican_literature", "language": "any"},
        {"query": "asian literature", "category": "asian_literature", "language": "any"},
        {"query": "indigenous knowledge", "category": "indigenous_knowledge", "language": "any"},
        
        # Time periods
        {"query": "classic literature", "category": "classics", "language": "any"},
        {"query": "contemporary fiction published:2015-2023", "category": "contemporary", "language": "any"},
        {"query": "20th century literature published:1900-1999", "category": "20th_century", "language": "any"},
        
        # Formats and genres
        {"query": "graphic novel", "category": "graphic_novels", "language": "any"},
        {"query": "manga", "category": "manga", "language": "ja"},
        {"query": "poetry anthology", "category": "poetry", "language": "any"},
        {"query": "folk tales", "category": "folklore", "language": "any"},
        
        # Specialized subject matter
        {"query": "feminist literature", "category": "feminist_lit", "language": "any"},
        {"query": "LGBTQ literature", "category": "lgbtq_lit", "language": "any"},
        {"query": "postcolonial studies", "category": "postcolonial", "language": "any"},
        {"query": "environmental studies", "category": "environmental", "language": "any"}
    ]
    
    TOTAL_BOOKS_NEEDED = 500
    API_KEY = None  # Replace with your key if needed

    # Build diverse dataset
    diverse_books = build_diverse_dataset(SEARCH_CONFIGS, TOTAL_BOOKS_NEEDED, API_KEY)
    
    print(f"Total unique books collected: {len(diverse_books)}")
    
    # Save the dataset to a JSON file
    with open('diverse_books_dataset_500.json', 'w') as f:
        json.dump(diverse_books, indent=2, fp=f)
    
    print("Dataset saved to diverse_books_dataset_500.json")