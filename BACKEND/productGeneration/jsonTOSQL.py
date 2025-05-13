"""
VS Code-friendly JSON to SQL Converter

This script converts a book JSON file to SQL INSERT statements.
Just configure the input and output files below, then hit the Run button in VS Code.
"""

import json
import os

# ===== CONFIGURE THESE SETTINGS =====
# Path to your JSON file with book data (relative to this script or absolute path)
INPUT_JSON_FILE = "diverse_books_dataset_500.json"  # Change this to your JSON file

# Where to save the SQL output (leave blank to use input filename + "_inserts.sql")
OUTPUT_SQL_FILE = ""  
# ===================================

def convert_json_to_sql():
    """Convert the configured JSON file to SQL INSERT statements."""
    global INPUT_JSON_FILE
    
    # Handle the case where the file doesn't exist
    if not os.path.exists(INPUT_JSON_FILE):
        print(f"File not found: {INPUT_JSON_FILE}")
        
        # If we're in a VS Code environment, offer to create a sample file
        sample_path = os.path.join(os.path.dirname(__file__), "sample_book.json")
        create_sample = input(f"Would you like to create a sample file at {sample_path}? (y/n): ")
        
        if create_sample.lower() == 'y':
            create_sample_file(sample_path)
            INPUT_JSON_FILE = sample_path
        else:
            return
    
    # Load the JSON data
    with open(INPUT_JSON_FILE, 'r', encoding='utf-8') as file:
        try:
            data = json.load(file)
        except json.JSONDecodeError:
            print(f"Error: {INPUT_JSON_FILE} is not a valid JSON file")
            return
    
    # Handle single book or array of books
    if isinstance(data, dict):
        books = [data]
    else:
        books = data
    
    print(f"Processing {len(books)} book(s) from {INPUT_JSON_FILE}...")
    
    # Track categories
    seen_categories = set()
    category_ids = {}
    
    # Store SQL statements
    sql_statements = []
    
    # Process each book
    for book in books:
        # Add product insert statement
        sql_statements.append(generate_product_insert(book))
        
        # Process categories
        categories = book.get('categories', [])
        for category in categories:
            if category not in seen_categories:
                seen_categories.add(category)
                category_id = f"cat_{category.lower().replace(' ', '_')}"
                category_ids[category] = category_id
                
                # Add category insert
                sql_statements.append(
                    f"INSERT INTO categories (id, name, searchable) VALUES "
                    f"('{category_id}', '{escape_sql_string(category)}', TRUE);"
                )
            
            # Add product-category relationship
            sql_statements.append(
                f"INSERT INTO product_category (product_id, category_id) VALUES "
                f"('{book['id']}', '{category_ids[category]}');"
            )
    
    # Prepare SQL output
    sql_output = "\n".join(sql_statements)
    
    # Determine output file path
    if not OUTPUT_SQL_FILE:
        output_path = os.path.splitext(INPUT_JSON_FILE)[0] + '_inserts.sql'
    else:
        output_path = OUTPUT_SQL_FILE
    
    # Write SQL to file
    with open(output_path, 'w', encoding='utf-8') as f:
        f.write(sql_output)
    
    print(f"Success! {len(books)} book(s) processed.")
    print(f"SQL statements saved to: {output_path}")
    
    # Show sample of the output
    print("\nSample of generated SQL:")
    lines = sql_output.split('\n')
    for i in range(min(5, len(lines))):
        print(lines[i])
    
    if len(lines) > 5:
        print("... (more statements)")

def generate_product_insert(book):
    """Generate an INSERT statement for the products table."""
    product_fields = [
        "id", "title", "description", "isbn13", "publishedDate", 
        "publisher", "author", "pageCount", "maturityRating", 
        "language", "smallThumbnail", "thumbnail", "accessibleIn", 
        "ratingsCount"
    ]
    
    # Build values
    values = []
    for field in product_fields:
        if field not in book or book[field] is None:
            values.append('NULL')
        elif isinstance(book[field], (int, float)):
            values.append(str(book[field]))
        else:
            values.append(f"'{escape_sql_string(str(book[field]))}'")
    
    # Create INSERT statement
    fields_str = ", ".join(product_fields)
    values_str = ", ".join(values)
    
    return f"INSERT INTO products ({fields_str}) VALUES ({values_str});"

def escape_sql_string(value):
    """Escape quotes for SQL strings."""
    if value is None:
        return 'NULL'
    return str(value).replace("'", "''").replace("\\", "\\\\")

def create_sample_file(path):
    """Create a sample JSON file with book data."""
    sample_book = {
        "id": "negHAAAACAAJ",
        "title": "Chess Highlights of the 20th Century",
        "author": "Graham Burgess",
        "publisher": "Gambit Publications",
        "publishedDate": "1999",
        "description": "The best chess, 1900-1999, in historical context: like the rest of the world, the game of chess has changed enormously during the 20th century. This book surveys these developments by focusing on the top events, greatest achievements and most brilliant games, year-by-year.",
        "categories": [
            "Chess"
        ],
        "pageCount": 0,
        "maturityRating": "NOT_MATURE",
        "language": "en",
        "smallThumbnail": "http://books.google.com/books/content?id=negHAAAACAAJ&printsec=frontcover&img=1&zoom=5&source=gbs_api",
        "thumbnail": "http://books.google.com/books/content?id=negHAAAACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api",
        "accessibleIn": "ZA",
        "ratingsCount": 1737,
        "isbn13": "9781901983210"
    }
    
    # Write the sample file
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(sample_book, f, indent=2)
    
    print(f"Sample book file created at: {path}")

if __name__ == "__main__":
    # This is what runs when you click the Run button in VS Code
    convert_json_to_sql()